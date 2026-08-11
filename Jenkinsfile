pipeline {
  agent any

  options {
    timeout(time: 15, unit: 'MINUTES')  
    disableConcurrentBuilds()
  }

  environment {
    REGISTRY        = 'harbor.telepedia.org'
    APP_IMAGE       = 'harbor.telepedia.org/mediawiki/app'
    WEB_IMAGE       = 'harbor.telepedia.org/mediawiki/web'
    DOCKER_BUILDKIT = '1'
  }

  stages {
    stage('Checkout') {
      steps { checkout scm }
    }

    stage('Compute release') {
      steps {
        script {
          // flat counter release-NNN; only bump when there are new commits since the last tag
          sh 'git fetch --tags --quiet || true'
          def last = sh(returnStdout: true, script: "git tag -l 'release-*' | sort -V | tail -1").trim()
          def unreleased = last ? sh(returnStdout: true, script: "git log --oneline ${last}..HEAD").trim() : 'initial'

          if (last && unreleased.isEmpty()) {
            env.RELEASE = last          // nothing new — redeploy the same tag
            env.NEW = 'false'
          } else {
            def n = last ? (last.tokenize('-')[1].toInteger() + 1) : 1
            env.RELEASE = 'release-' + n.toString().padLeft(3, '0')
            env.NEW = 'true'
          }
          env.VCS_REF = sh(returnStdout: true, script: 'git rev-parse --short=7 HEAD').trim()
          echo "Release: ${env.RELEASE} (new=${env.NEW}) @ ${env.VCS_REF}"
        }
      }
    }

    stage('Build + push') {
      when { environment name: 'NEW', value: 'true' }
      steps {
        withCredentials([
          string(credentialsId: 'github-ext-token', variable: 'GH_TOKEN'),
          usernamePassword(credentialsId: 'gitlab-ext', usernameVariable: 'GITLAB_USR', passwordVariable: 'GITLAB_PSW'),
          usernamePassword(credentialsId: 'harbor-robot', usernameVariable: 'HARBOR_USR', passwordVariable: 'HARBOR_PSW'),
        ]) {
          sh '''
            echo "$HARBOR_PSW" | docker login "$REGISTRY" -u "$HARBOR_USR" --password-stdin
            for target in app web; do
              img="$REGISTRY/mediawiki/${target}"
              docker build \
                --secret id=github_token,env=GH_TOKEN \
                --secret id=gitlab_user,env=GITLAB_USR \
                --secret id=gitlab_token,env=GITLAB_PSW \
                --target ${target} \
                --build-arg RELEASE="$RELEASE" \
                --build-arg VCS_REF="$VCS_REF" \
                -f docker/Containerfile \
                -t ${img}:${RELEASE} -t ${img}:latest \
                .
              docker push ${img}:${RELEASE}
              docker push ${img}:latest
            done
            docker logout "$REGISTRY"
          '''
        }
      }
    }

    stage('Tag release') {
      when { environment name: 'NEW', value: 'true' }
      steps {
        withCredentials([string(credentialsId: 'github-ext-token', variable: 'GH_TOKEN')]) {
          sh '''
            git config user.email "jenkins@telepedia.net"
            git config user.name  "MonsieurJenkins"
            git tag -a "$RELEASE" -m "$RELEASE"
            git push "https://x-access-token:${GH_TOKEN}@github.com/Telepedia/mw-config" "$RELEASE"
          '''
        }
      }
    }

    stage('Deploy') {
      steps {
        build job: 'mediawiki-deploy',
              parameters: [string(name: 'release', value: env.RELEASE)],
              wait: true
      }
    }
  }

  post {
    always {
      sh 'docker image prune -f || true'
    }
  }
}
