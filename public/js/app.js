$(function() 
{
    // Submit the title on focus out.
    $("#list-title-form input").on("focusout", function() {
        submitForm("list-title-form");
    });

    // Submit priority on click.
    $("#priority-btn").on("click", function() {
        submitForm("priority-form");
    })

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

/**
 * Submit form without submit buttons.
 * @param {string} formId the form id to submit
 */
function submitForm(formId)
{
    document.getElementById(formId).submit();
}