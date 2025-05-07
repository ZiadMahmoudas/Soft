/* nav-bar: focus*/
let navLinks = document.querySelectorAll(".nav-link");
function nav(){
navLinks.forEach(link => {
    link.addEventListener("click", function () {
        navLinks.forEach(l => l.classList.remove("active"));
        this.classList.add("active");
        setTimeout(() => {
            this.classList.remove("active");
        }, 10000000); 
    });
})
}

export {nav}