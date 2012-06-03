$(document).ready(function() {

    $('.tabbable a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
});
