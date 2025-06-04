 const toggle = document.getElementById("menu-toggle");
    const nav = document.getElementById("nav");

    toggle.addEventListener("click", () => {
      nav.style.display = nav.style.display === "flex" ? "none" : "flex";
    });