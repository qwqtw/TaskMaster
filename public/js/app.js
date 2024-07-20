$(function() {
    // Show add task form
    $("#task-add-btn").on("click", function() {
        $("#task-add-form").toggle(200);
        $("#task-add-btn").toggleClass("hide");

        if ($("#task-add-btn").hasClass("hide")) {
            $("#task-add-form textarea").focus();
        }
    })

    // Reroute to selected list
    $(".list-item input[type=radio]").on("click", route);
    $(".task-content").on("click", route);

})

function route(event)
{
    window.location.href = $(event["target"]).attr("data-url");
}