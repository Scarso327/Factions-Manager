class Application {

    constructor() {
        $("a[theme-toggle]").each(function () {
            $(this).click(function () {
                Application.toggleTheme($(this));
            });
        });
    }

    static toggleTheme(button) {
        $.ajax({
            type: "POST",
            url: "api/toggleTheme/",
            success: function(response){
                var body = $("#main-body");
                var icon = button.children().first();

                if (body.hasClass("dark")) {
                    body.removeClass("dark");
                    icon.removeClass("fa-times-circle");
                    icon.addClass("fa-check-circle");
                    button.removeClass("active");
                } else {
                    body.addClass("dark");
                    icon.removeClass("fa-check-circle");
                    icon.addClass("fa-times-circle");
                    button.addClass("active");
                }
            }
        });
    }
}

$(document).ready(function () {
    app = new Application();
});