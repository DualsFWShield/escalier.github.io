document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    form.addEventListener("submit", function(event) {
        event.preventDefault();
        const plis = document.querySelector("#plis").value;
        const points = document.querySelector("#points").value;
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "calculate.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("plis=" + plis + "&points=" + points);
    });
});
