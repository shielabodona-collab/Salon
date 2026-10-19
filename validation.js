
document.addEventListener("DOMContentLoaded", () => {
    const menuButton = document.getElementById("menuToggle");
    const nav = document.getElementById("mainNav");

    if (menuButton && nav) {
        menuButton.addEventListener("click", () => {
            const opened = nav.classList.toggle("open");
            menuButton.setAttribute("aria-expanded", String(opened));
        });
    }

    document.querySelectorAll("form[data-validate]").forEach(form => {
        form.addEventListener("submit", event => {
            let valid = true;
            const errors = [];

            const name = form.querySelector("[name='name']");
            if (name && !/^[A-Za-z ]{2,100}$/.test(name.value.trim())) {
                valid = false;
                errors.push("Name must contain letters and spaces only.");
            }

            const phone = form.querySelector("[name='phone']");
            if (phone && !/^[0-9]{10,15}$/.test(phone.value.trim())) {
                valid = false;
                errors.push("Phone must contain 10 to 15 digits only.");
            }

            const email = form.querySelector("[name='email']");
            if (email && !/^[A-Za-z0-9._%+-]+@gmail\.com$/i.test(email.value.trim())) {
                valid = false;
                errors.push("Please use a valid @gmail.com address.");
            }

            const password = form.querySelector("[name='password']");
            if (password && !/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9\s])\S{8,}$/.test(password.value)) {
                valid = false;
                errors.push("Password needs at least 8 characters, a letter, a number, and a special character.");
            }

            const confirm = form.querySelector("[name='confirm_password']");
            if (confirm && password && confirm.value !== password.value) {
                valid = false;
                errors.push("Passwords do not match.");
            }

            let box = form.querySelector(".client-errors");
            if (!box) {
                box = document.createElement("div");
                box.className = "error client-errors";
                box.setAttribute("role", "alert");
                form.prepend(box);
            }

            box.textContent = errors.join(" ");
            box.hidden = errors.length === 0;

            if (!valid) event.preventDefault();
        });
    });
});
