$(function() 
{
    // Add scroll event listener
    $(".scrollbar").on("scroll", function(event) {
        $(".scroll-indicator").css("visibility", ($(event.target).scrollTop() == 0) ? "visible" : "hidden");
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
    $(".list-item").on("click", route);

    // Update backend/frontend

    // Submit the title on focus out.
    $("#list-title-form input").on("focusout", updateTitle);
    // Delete list
    $(".list-delete").on("click", deleteList);

    // Toggle task
    $(".task-content").on("click", toggleTask);
    // Delete task
    $(".task-delete").on("click", deleteTask);

})

function route(event)
{
    const target = $(event.currentTarget);
    let url = target.attr("data-url");

    if (typeof(url) === "undefined") {
        url = target.closest("li").attr("data-url");
    }

    window.location.href = url;
}

/**
 * Submit form without submit buttons.
 * @param {string} formId the form id to submit
 */
function submitForm(formId)
{
    document.getElementById(formId).submit();
}


function updateTitle(event)
{
    const form = $("#list-title-form");
    const listId = $("#list-title-form").data("id");

    const input = $("#list-title-form input[name=title]");

    $.post(
        `${form.attr("action")}`, 
        {"title": input.val()})
        .done(function(newTitle) {
            if (newTitle !== 0) {
                // Set up the new title and in the list
                input.val(newTitle);
                $("#l-" + listId + " span").text(newTitle);
            }
    })
}


/**
 * Toggle the task's completed status
 * @param {event} event 
 */
function toggleTask(event)
{
    const target = $(event.currentTarget);
    const li = target.closest("li");

    $.get(`${li.attr("data-url")}/toggle`, function(isCompleted) {
        if (isCompleted) {

            // Update elements
            for (let name of ["task-content", "priority", "checkmark", "task-date"]) {
                li.find('.' + name).toggleClass("completed");
            }
        }
    });
}

/**
 * Delete a task
 * @param {event} event 
 */
function deleteTask(event)
{
    const target = $(event.currentTarget);
    const li = target.closest("li");

    $.ajax({
        url:`${li.attr("data-url")}/delete`,
        type: "DELETE",
        success: function(is_completed) {
            if (is_completed) {

                li.remove();
            }
        }
    });
}

/**
 * Delete a list
 * @param {event} event 
 */
function deleteList(event)
{
    const target = $(event.currentTarget);
    const li = target.closest("li");

    $.ajax({
        'url': `${li.attr("data-url")}/delete`,
        type: "DELETE",
        success: function(is_completed) {
            if (is_completed) {

                li.remove();
            }
        }
    })
}
