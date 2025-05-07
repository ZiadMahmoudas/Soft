
import { loadTheme, initThemeListeners } from "../../componentJS/theme-mode.js";
import { nav} from "../../componentJS/toolsFornavbar.js";
nav();
const params = new URLSearchParams(window.location.search);
const title = params.get("title");
const image = params.get("image");

document.getElementById("title").innerText = title;
const imagePath = "../../componentImages/img/" + image;
document.getElementById("image").src = imagePath;


    flatpickr("#dest", {
       dateFormat: "d/m/Y",
      minDate: "today",
      defaultDate: "today",
       
    });
flatpickr("#source", {
    dateFormat: "d/m/Y",
    minDate: "today",
    defaultDate: "today"
  })