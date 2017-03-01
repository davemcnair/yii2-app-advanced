jQuery(document).ready(function($) {
    if (document.location.href.indexOf('#print') > -1) {
        $("#content").print({globalStyles: true});
        // console.log('hello');
    }
});