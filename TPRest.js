/**
 * Wrapper around the fetch() API to avoid using mw.Rest - this is because whilst the action API always returns
 * status 200 even on error, the Rest API will return the status code we give it, and a generic 'http' error
 *
 * Whilst it is still possible for use to get the error, this is a lot of messing about, whereas we can just use fetch
 * and get the error exactly
 * See below the issue
 * @link: https://doc.wikimedia.org/mediawiki-core/master/js/mediawiki.api_rest.js.html#line201
 *
 * For our own sanity, and developer productivity, we'll just use this. This is here in the root directory as it doesn't
 * really belong to any extension and should be used by all. Extensions expect it to be available
 * from $wgResourceModules['telepedia-fetch'] and should be added as such in LocalSettings.php
 * @since REL1_43
 * @author Original Authority
 */
class TPRest {
    constructor() {
        this.baseUrl = mw.util.wikiScript( 'rest' );
    }

    async request( path, options = {} ) {
        const {
            method = 'GET',
            body = null,
            headers = {},
            ...fetchOptions
        } = options;

        const config = {
            method,
            headers: {
                'Content-Type': 'application/json',
                ...headers
            },
            ...fetchOptions
        };

        if ( body && method !== 'GET' ) {
            config.body = JSON.stringify( body );
        }

        try {
            const response = await fetch( `${this.baseUrl}${path}`, config );

            if ( response.status === 204 ) {
                // if the response is created from ->createNoContent(), then just return a bogus success
                // message to the caller so it doesn't try to parse the JSON if there was none
                // if not, doing a try/catch block will always execute the catch block
                return { success: true };
            }
            
            const data = await response.json();

            // Error should always be returned from a subclass of SimpleClass::handler with the
            // [ 'error' => 'Error here' ] structure such as
            // $this->getResponseFactory()->createHttpError( 500, [ 'error' => 'some error here' ]
            if ( !response.ok || data?.error ) {
                throw new Error( data?.error || `HTTP ${response.status}: ${response.statusText}` );
            }

            return data;

        } catch ( error ) {
            // Rethrow with meaningful error message; the caller is responsible for catching this error and presenting
            // it to the user. We mostly use new Toast( error.message, {...} ).show()
            if ( error.message ) {
                throw error;
            }

            throw new Error( 'Network request failed' );
        }
    }

    /**
     * GET request wrapper
     * @param path
     * @param params
     * @param headers
     * @returns {Promise<any|undefined>}
     */
    async get( path, params = {}, headers = {} ) {
        const url = new URL( `${this.baseUrl}${path}`, window.location.origin );
        Object.entries( params ).forEach( ( [ key, value ] ) => {
            url.searchParams.append( key, value );
        } );

        const config = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                ...headers
            }
        };

        const response = await fetch( url.toString(), config );
        const data = await response.json();

        if ( !response.ok || data?.error ) {
            throw new Error( data?.error || `HTTP ${response.status}: ${response.statusText}` );
        }

        return data;
    }

    /**
     * POST request
     * @param path
     * @param body
     * @param headers
     * @returns {Promise<any|undefined>}
     */
    async post( path, body, headers = {} ) {
        return this.request( path, {
            method: 'POST',
            body,
            headers
        } );
    }

    /**
     * PUT request
     * @param path
     * @param body
     * @param headers
     * @returns {Promise<any|undefined>}
     */
    async put( path, body, headers = {} ) {
        return this.request( path, {
            method: 'PUT',
            body,
            headers
        } );
    }

    /**
     * PATCH request (improvement over mw.Rest which doesn't support PATCH
     * @param path
     * @param body
     * @param headers
     * @returns {Promise<any|undefined>}
     */
    async patch( path, body, headers = {} ) {
        return this.request( path, {
            method: 'PATCH',
            body,
            headers
        } );
    }

    /**
     * DELETE request
     * @param path
     * @param body
     * @param headers
     * @returns {Promise<any|undefined>}
     */
    async delete( path, body = {}, headers = {} ) {
        return this.request( path, {
            method: 'DELETE',
            body,
            headers
        } );
    }
}

module.exports = new TPRest();