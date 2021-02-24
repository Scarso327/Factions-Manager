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
            url: window.location.protocol + "//" + window.location.host + "/api/toggleTheme/",
            success: function(response){
                var body = $("#main-body");
                var icon = button.children().first();

                if (body.hasClass("dark")) {
                    body.removeClass("dark");
                    icon.removeClass("fa-sun");
                    icon.addClass("fa-moon");
                    icon.parent().parent().attr("title", "Dark Theme");
                    button.removeClass("active");
                } else {
                    body.addClass("dark");
                    icon.removeClass("fa-moon");
                    icon.addClass("fa-sun");
                    icon.parent().parent().attr("title", "Dark Theme");
                    button.addClass("active");
                }
            }
        });
    }
}

$(document).ready(function () {
    app = new Application();
});