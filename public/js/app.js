$(function() {
    // Show add task form
    $("#task-add-btn").on("click", function() {
        $("#task-add-form").toggle(200);
        $("#task-add-form textarea").focus();
        $("#task-add-btn").toggleClass("hide");
    })

    // Reroute to selected list
    $(".list-item input[type=radio]").on("click", route);
})

function route(event)
{
    window.location.href = $(event["target"]).attr('data-url');
}