const newButton = document.querySelector("#printLabelsButton") as HTMLElement | null;
const widgetToolbox = document.querySelector(".widget-toolbox") as HTMLElement | null;

if (newButton && widgetToolbox) {
    widgetToolbox.appendChild(newButton);
}


const assignTemplateSelectAllVCheckbox = document.getElementById('assign_template_select_all') as HTMLInputElement;
const assignedProjectsCheckboxes = document.querySelectorAll<HTMLInputElement>('input[name="assigned_projects[]"]');


if (assignTemplateSelectAllVCheckbox) {
    assignTemplateSelectAllVCheckbox.onchange = () => {
        const checked = assignTemplateSelectAllVCheckbox.checked;
        assignedProjectsCheckboxes.forEach(checkbox => {
            checkbox.checked = checked
        });
    }
}
