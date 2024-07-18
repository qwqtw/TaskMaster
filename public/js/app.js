$(function() {
    // Show add task form
    $("#btn-add-task").on("click", function() {
        $("#form-add-task").show(300);
    })

    // Reroute to selected list
    $(".list-item input[type=radio]").on("click", route);
})

function route(event)
{
    window.location.href = $(event["target"]).attr('data-url');
}