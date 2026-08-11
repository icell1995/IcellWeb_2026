$(document).ready(function () {
    $("#reload").click(function () {
        var refreshUrl = $(this).data("refresh-url");
        $.ajax({
            url: refreshUrl,
            type: "GET",
            success: function (data) {
                $(".captcha span").html(data.captcha);
            },
        });
    });

    // $(".toggle-password").click(function () {
    //     $(this).toggleClass("fa-eye fa-eye-slash");
    //     var input = $($(this).attr("toggle"));
    //     if (input.attr("type") == "password") {
    //         input.attr("type", "text");
    //     } else {
    //         input.attr("type", "password");
    //     }
    // });
});

document.addEventListener("DOMContentLoaded", function () {
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("input[name='password']");

    togglePassword.addEventListener("click", function () {
        const type =
            password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);
        this.classList.toggle("bi-eye");
        this.classList.toggle("bi-eye-slash");
    });
});
