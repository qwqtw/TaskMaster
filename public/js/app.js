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
    $("#task-add-btn").on("click", toggleAddTask);

    // Reroute to selected list
    $(".list-item").on("click", route);

    // Update backend/frontend

    // Submit the title on focus out.
    $("#list-title-form input").on("focusout", updateTitle);
    $("#list-title-form").on("submit", updateTitle);
    // Delete list
    $(".list-delete").on("click", deleteList);

    // Submit add task form
    $("#task-add-form").on("submit", addOrUpdateTask);
    // Toggle task
    $("#tasks-container ul").on("click", "li", toggleTask);
    // Edit task
    $("#tasks-container ul").on("click", "li .task-edit", editTask);
    // Delete task
    $("#tasks-container ul").on("click", "li .task-delete", deleteTask);

})

function toggleAddTask(event)
{
    $("#task-add-form").toggle(200);
    $("#task-add-btn").toggleClass("hide");

    // Showing the dialog and hiding the default button
    if ($("#task-add-btn").hasClass("hide")) {
        $("#task-add-form textarea").focus();
    }
    // Hiding the dialog
    else {
        $("#task-add-form").removeClass("edit");
        $("#task-add-form").trigger("reset");
    }
}

function route(event)
{
    const target = $(event.currentTarget);
    let url = target.data("url");

    if (typeof(url) === "undefined") {
        url = target.closest("li").data("url");
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


/**
 * Update the selected list title.
 * @param {event} event 
 */
function updateTitle(event)
{
    event.preventDefault();

    const form = $("#list-title-form");
    const listId = $("#list-title-form").data("id");
    const input = $("#list-title-form input[name=title]");

    $.post(`${form.attr("action")}`, {"title": input.val()})
        .done(function(newTitle) {
            if (newTitle !== 0) {
                // Set up the new title and in the list
                input.val(newTitle);
                $("#l-" + listId + " span").text(newTitle);
                // Clear focus
                if (event.type === "submit") {
                    input.blur();
                }
            }
        })
}

/**
 * Create the task through json response 
 * to practice a different approach to improve ux.
 * @param {event} event 
 */
function addOrUpdateTask(event)
{
    event.preventDefault();

    const form = event.currentTarget;
    const formData = new FormData(form);
    const isUpdate = $(form).hasClass("edit");

    $.ajax({
        url: `${form.action}${isUpdate ? "/" + formData.get("id") + "/update" : "/create"}`,
        type: "POST",
        data: formData,
        // Make formData work w jquery
        contentType: false,
        processData: false,
        
        success: function(taskData) {
            if (taskData !== "{}") {

                const task = JSON.parse(taskData);

                if (isUpdate) {
                    updateTask(task);
                }
                // Create a new task
                else {
                    const li = createTask(task);
                    $("#tasks-container ul").append(li);
                    window.location.hash = "t-" + task.id;
                }

                // Force scroll to the new task;
                toggleAddTask();
            }
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

    $.get(`${li.data("url")}/toggle`)
        .done(function(isCompleted) {
            if (isCompleted) {
                // Update elements
                for (let name of ["task-content", "priority", "checkmark", "task-date"]) {
                    li.find('.' + name).toggleClass("completed");
                }
            }
    });
}


function editTask(event)
{
    event.stopPropagation();
    // Change the add icon
    $("#task-add-form").addClass("edit");
    //$("#task-add-form button[type=submit]").toggleClass("hide");

    const task = $(event.currentTarget).closest("li");

    // Get the entry
    $.get(task.data("url"))
        .done(function(task) {
            // Fill the information into the form
            if (task !== "{}") {
                const taskData = JSON.parse(task);

                // Hidden input id
                $(`#task-add-form input[name="id"]`).val(taskData.id);
                // Fill in existing task data
                $(`#task-add-form option[value=${taskData.priority}]`).attr("selected", "selected");
                $("#task-add-form textarea[name=content]").val(taskData.content);
                $("#task-add-form input[type=date]").val(taskData.due_date);

                toggleAddTask();
            }
    })
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
        url:`${li.data("url")}/delete`,
        type: "DELETE",
        success: function(isCompleted) {
            if (isCompleted) {

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
    event.stopPropagation(); // action within li click
    const target = $(event.currentTarget);
    const li = target.closest("li");

    $.ajax({
        'url': `${li.data("url")}/delete`,
        type: "DELETE",
        success: function(isCompleted) {
            if (isCompleted) {
                /* We need to load a new list*/
                if (li.hasClass("active")) {
                    window.location.href = li.data("url");
                }

                li.remove();
            }
        }
    })
}


/**
 * Create the task element.
 * @param {json} jsonData the json object containing the task data
 * @return {HTMLElement} li the created task li
 */
function createTask(jsonData)
{
    const task = jsonData;
    const taskCompleted = task.is_completed ? "completed" : "";
    // set falsey values to an empty string
    task.due_date = task.due_date || ""; 

    // Task li
    const li = $(`<li id="t-${task.id}" data-id="${task.id}" data-url="${task.base_task_url}" class="d-flex w-100 mb-4"></li>`);
    li.html(`
        <div>
            <div class="marker priority priority-${task.priority} ${taskCompleted}"><i class="fa-solid fa-circle"></i></div>
            <div class="marker checkmark ${taskCompleted}"><i class="fa-solid fa-circle-check"></i></div>
        </div>
        <div class="d-flex flex-column w-100">
            <p class="mb-2 task-content ${taskCompleted}">${task.content.replace(/\n/g, "<br>\n")}</p>
            <div class="d-flex">
                <div class="${taskCompleted} ${task.due_date !== "" ? "due-date" : ""} task-date justify-content-center align-items-center rounded-5">${task.due_date}</div>
            </div>
        </div>
    `)

    // Add interface buttons and events
    const divButtons = $(`<div class="text-center"></div>`);
    li.append(divButtons);
    
    // Edit button
    const editButton = $(`<button type="button" class="${taskCompleted} task-icon task-edit"><i class="fa-solid fa-pen-to-square"></i></button>`);
    divButtons.append(editButton);
    
    // Delete button
    const deleteButton = $(`<button type="button" class="task-icon task-delete"><i class="fa-solid fa-trash-can"></i></button>`);
    divButtons.append(deleteButton);

    return li;
}

/**
 * Update the task li with new task data.
 * @param {json} jsonData the json task data
 */
function updateTask(jsonData)
{
    $(`#t-${jsonData.id}`).replaceWith(createTask(jsonData));
}
