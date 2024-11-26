function timerStarten(duratie, display, startTime) {
    var timer = duratie - Math.floor((Date.now() / 1000) - startTime);
    var countdownFinished = false;

    var intervalId = setInterval(function () {
        if (!countdownFinished) {
            var minutes = parseInt(timer / 60, 10);
            var seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                timer = 0;
                countdownFinished = true;
                $('#eindModal').modal('show');
            }
        }
    }, 1000);

    $('#eindModal').on('hidden.bs.modal', function () {
        clearInterval(intervalId);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("quizForm");
    var submitButton = document.getElementById("submitButton");

    if (form) {
        var prevButton = document.getElementById("prevButton");
        var nextButton = document.getElementById("nextButton");
        var currentStep = 0;

        function updateButtons() {
            prevButton.disabled = currentStep === 0;
            nextButton.style.display = (currentStep < totalSteps - 1) ? "block" : "none";
            submitButton.style.display = (currentStep === totalSteps - 1) ? "block" : "none";

            submitButton.disabled = (currentStep === totalSteps - 1) ? false : true;
        }

        function updateTabVisibility() {
            var tabContent = document.getElementById("questionsTabContent");
            var tabPanes = tabContent ? tabContent.children : [];
        
            console.log("Total tab panes:", tabPanes.length);
        
            for (var i = 0; i < tabPanes.length; i++) {
                tabPanes[i].classList.remove('active', 'show');
            }
        
            if (tabPanes[currentStep]) {
                tabPanes[currentStep].classList.add('active', 'show');
            } else {
                console.error("No tab pane found for step:", currentStep);
            }
        }
        

        prevButton.addEventListener("click", function () {
            if (currentStep > 0) {
                currentStep--;
                updateButtons();
                updateTabVisibility();
            }
        });

        nextButton.addEventListener("click", function () {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                updateButtons();
                updateTabVisibility();
            }
        });

        submitButton.addEventListener("click", function () {
            form.submit();
        });

        function goToNextTab() {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                updateButtons();
                updateTabVisibility();
            }
        }

        form.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();

                if (currentStep === totalSteps - 1) {
                    console.log("Last step");
                    submitButton.click();
                } else {
                    goToNextTab();
                    var nextInputField = document.querySelector('.tab-pane.active input[type="number"]');
                    if (nextInputField) {
                        nextInputField.focus();
                    }
                }
            }
        });             

        updateButtons();
        updateTabVisibility();
    }
});
