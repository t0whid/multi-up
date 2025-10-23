// ---------- LIGHT/DARK THEME TOGGLE ----------
const themeToggleBtn = document.querySelector(".theme-toggle");
const html = document.documentElement;

// Function to apply theme
function applyTheme(theme) {
    if(theme === "dark") {
        html.classList.add("dark-theme");
        html.classList.remove("light-theme");
        themeToggleBtn.innerHTML = '<i class="bi bi-sun-fill"></i>'; // show sun icon
    } else {
        html.classList.add("light-theme");
        html.classList.remove("dark-theme");
        themeToggleBtn.innerHTML = '<i class="bi bi-moon-stars"></i>'; // show moon icon
        themeToggleBtn.style.color = "#000";
    }
}

// Load saved theme from localStorage
let savedTheme = localStorage.getItem("theme") || "dark"; // default dark
applyTheme(savedTheme);

// Toggle theme on button click
themeToggleBtn.addEventListener("click", () => {
    let currentTheme = html.classList.contains("dark-theme") ? "dark" : "light";
    let newTheme = currentTheme === "dark" ? "light" : "dark";
    applyTheme(newTheme);
    localStorage.setItem("theme", newTheme);
});
