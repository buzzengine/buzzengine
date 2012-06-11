$(document).ready(function() {
    $('.tabbable .nav a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
});
