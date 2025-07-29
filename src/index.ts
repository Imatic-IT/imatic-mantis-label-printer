const newButton = document.querySelector("#printLabelsButton") as HTMLElement | null;
const widgetToolbox = document.querySelector(".widget-toolbox") as HTMLElement | null;

if (newButton && widgetToolbox) {
    widgetToolbox.appendChild(newButton);
}