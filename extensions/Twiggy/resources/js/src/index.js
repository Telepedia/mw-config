import Twig from 'twig';

export default class Twiggy {
    static init(mw) {
        Twig.extendFunction("wfMessage", function(msg, output, ...params) {
            return new mw.Api().loadMessagesIfMissing([msg])
            .then(() => {
                return mw.message( msg, ...params )[output]();
            });
        });
    }
}
window.Twig = Twig;
window.Twiggy = Twiggy;
