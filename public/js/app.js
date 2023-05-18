$.LoadingOverlaySetup({
    background: "rgba(218, 223, 225, 0.8)",
    fontawesome: "bi bi-cup-hot-fill",
    image: "",
    fontawesomeAnimation: "2000ms pulse",
    fontawesomeAutoResize: true,
});

$(document).ajaxStart(function () {
    $.LoadingOverlay("show");
});

$(document).ajaxStop(function () {
    $.LoadingOverlay("hide");
});

$('a[data-loading],button[data-loading]').each(function () {
    $(this).click(function () {
        $.LoadingOverlay("show");
    });
});