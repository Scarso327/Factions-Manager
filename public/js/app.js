class Application {

    constructor() {
        $("a[theme-toggle]").each(function () {
            $(this).click(function () {
                Application.toggleTheme($(this));
            });
        });

        $("a[staff-toggle]").each(function () {
            $(this).click(function () {
                Application.toggleStaff($(this));
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

    static toggleStaff(button) {
        $.ajax({
            type: "POST",
            url: window.location.protocol + "//" + window.location.host + "/api/toggleStaff/",
            success: function(response){
                if (button.html() == "Show Staff") {
                    button.html("Hide Staff");
                    
                    $("tr[hidden-staff]").each(function () {
                        $(this).removeClass("hide");
                    });
                } else {
                    button.html("Show Staff");

                    $("tr[hidden-staff]").each(function () {
                        $(this).addClass("hide");
                    });
                }
            }
        });
    }
}

$(document).ready(function () {
    app = new Application();
});