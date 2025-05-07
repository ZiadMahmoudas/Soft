let nav = document.querySelector(".navbar");
let btn = document.querySelector("#change-mode");
let awesome = document.querySelector(".fa-solid");
let arrowUp = document.getElementById("arrowUP");

function setDarkMode() {
    document.body.classList.remove("light-mode");
    document.body.classList.add("dark-mode");
    awesome.classList.remove("fa-sun");
    awesome.classList.add("fa-moon");
    awesome.style.color = "#fff";
    saveLocalStorage("dark");
    window.removeEventListener("scroll", LigthModeScroll);
    window.addEventListener("scroll", DarkModeScroll);
}

function setLightMode() {
    document.body.classList.remove("dark-mode");
    document.body.classList.add("light-mode");
    awesome.classList.add("fa-sun");
    awesome.classList.remove("fa-moon");
    awesome.style.color = "#FDCB58";
    saveLocalStorage("light");
    window.removeEventListener("scroll", DarkModeScroll);
    window.addEventListener("scroll", LigthModeScroll);
}

function saveLocalStorage(mode) {
    localStorage.setItem("theme", mode);
}

function DarkModeScroll() {
    if (window.scrollY >= 70) {
        nav.style.cssText = "box-shadow: 2px 3px 4px #fff; background-color: #000; transition: 0.5s ease-in-out; z-index: 4;";
    } else {
        nav.style.cssText = "transition: 0.5s ease-in-out;";
    }
}

function LigthModeScroll() {
    if (window.scrollY >= 70) {
        nav.style.cssText = "box-shadow: 2px 3px 4px #000; background-color: #fff; transition: 0.5s ease-in-out; z-index: 4;";
    } else {
        nav.style.cssText = "transition: 0.5s ease-in-out;";
    }
}

function loadTheme() {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        setDarkMode();
    } else {
        setLightMode();
    }
}

function initThemeListeners() {
    document.getElementById("toggle-box").addEventListener("click", function () {
        document.querySelector(".box").classList.toggle("act");

        btn.addEventListener("click", function () {
            if (document.body.classList.contains("light-mode")) {
                setDarkMode();
            } else {
                setLightMode();
            }
        });

        arrowUp.addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    });
}

export {
    loadTheme,
    initThemeListeners
};
