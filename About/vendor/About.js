import { loadTheme, initThemeListeners } from "../../componentJS/theme-mode.js";
import { nav} from "../../componentJS/toolsFornavbar.js";
nav();
loadTheme();
initThemeListeners();


let btnopen = document.getElementById("open");
let btnclose = document.getElementById("close");
let popItem = document.getElementById("popupItem");

btnopen.addEventListener("click", () => {
    popItem.style.cssText = "visibility: visible; transform: translate(-50%,-50%) scale(1); top: 50%";
});

btnclose.addEventListener("click", () => {
    popItem.style.cssText = "visibility: hidden";
});

document.addEventListener("keyup", function (e) {
    if (e.keyCode === 27) {
        popItem.style.cssText = "visibility: hidden";
    }
});