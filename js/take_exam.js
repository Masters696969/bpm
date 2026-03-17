document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const applicantId = body.dataset.id;
    
    // UI Elements
    const fullscreenOverlay = document.getElementById('fullscreenOverlay');
    const enterFullscreenBtn = document.getElementById('enterFullscreenBtn');
    const introScreen = document.getElementById('introScreen');
    const startExamBtn = document.getElementById('startExamBtn');
    const questionCard = document.getElementById('questionCard');
    const examFooter = document.getElementById('examFooter');
    const timerContainer = document.getElementById('timerContainer');
    const timerVal = document.getElementById('timerVal');
    const questionCounter = document.getElementById('questionCounter');
    const qText = document.getElementById('qText');
    const optionsList = document.getElementById('optionsList');
    
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    // State
    let currentQuestions = [];
    let currentIdx = 0;
    let selectedAnswers = {};
    let timerInterval = null;
    let timeLeft = 15 * 60;

    // 1. Fullscreen Logic
    enterFullscreenBtn.addEventListener('click', () => {
        requestFullscreen();
    });

    function requestFullscreen() {
        const docEl = document.documentElement;
        if (docEl.requestFullscreen) {
            docEl.requestFullscreen();
        } else if (docEl.webkitRequestFullscreen) {
            docEl.webkitRequestFullscreen();
        } else if (docEl.msRequestFullscreen) {
            docEl.msRequestFullscreen();
        }
        
        fullscreenOverlay.style.display = 'none';
        // After entering fullscreen, we fetch questions
        fetchQuestions();
    }

    // Monitor fullscreen exit
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            Swal.fire({
                title: 'Security Warning',
                text: 'The examination must be taken in full-screen mode. Please return to full-screen to continue.',
                icon: 'warning',
                confirmButtonText: 'Return to Full Screen',
                allowOutsideClick: false,
                backdrop: 'rgba(0,0,0,0.9)'
            }).then(() => {
                requestFullscreen();
            });
        }
    });

    // 2. Fetch Logic
    function fetchQuestions() {
        Swal.fire({
            title: 'Initializing Exam...',
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`backend/applicant_action.php?action=generate_exam&applicant_id=${applicantId}`)
            .then(res => res.json())
            .then(res => {
                Swal.close();
                if (res.success) {
                    currentQuestions = res.data;
                } else {
                    Swal.fire('Error', res.message, 'error').then(() => window.close());
                }
            })
            .catch(err => {
                Swal.close();
                Swal.fire('Error', 'Failed to fetch exam questions.', 'error');
            });
    }

    // 3. Start Exam Logic
    startExamBtn.addEventListener('click', () => {
        if (currentQuestions.length === 0) {
            Swal.fire('Wait', 'Questions are still loading or could not be found.', 'warning');
            return;
        }
        introScreen.style.display = 'none';
        questionCard.style.display = 'block';
        examFooter.style.display = 'flex';
        timerContainer.style.display = 'flex';
        
        renderQuestion();
        startTimer();
    });

    // 4. Timer Logic
    function startTimer() {
        timerInterval = setInterval(() => {
            timeLeft--;
            let mins = Math.floor(timeLeft / 60);
            let secs = timeLeft % 60;
            timerVal.textContent = `${mins}:${secs < 10 ? '0' : ''}${secs}`;

            if (timeLeft <= 300) { // 5 mins left
                timerVal.parentElement.style.background = 'rgba(239, 68, 68, 0.2)';
            }

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                autoSubmit();
            }
        }, 1000);
    }

    // 5. Render Question
    function renderQuestion() {
        const q = currentQuestions[currentIdx];
        questionCounter.textContent = `Question ${currentIdx + 1} of ${currentQuestions.length}`;
        qText.textContent = q.question_text;
        
        optionsList.innerHTML = '';
        const options = [
            { key: 'A', text: q.option_a },
            { key: 'B', text: q.option_b },
            { key: 'C', text: q.option_c },
            { key: 'D', text: q.option_d }
        ];

        options.forEach(opt => {
            const div = document.createElement('div');
            div.className = `option-item ${selectedAnswers[q.id] === opt.key ? 'selected' : ''}`;
            div.innerHTML = `
                <div class="option-key">${opt.key}</div>
                <div class="option-text">${opt.text}</div>
            `;

            div.onclick = () => {
                selectedAnswers[q.id] = opt.key;
                renderQuestion(); 
            };
            optionsList.appendChild(div);
        });

        // Navigation visibility
        prevBtn.style.visibility = (currentIdx === 0) ? 'hidden' : 'visible';
        if (currentIdx === currentQuestions.length - 1) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'flex';
        } else {
            nextBtn.style.display = 'flex';
            submitBtn.style.display = 'none';
        }
        
        if (window.lucide) lucide.createIcons();
    }

    prevBtn.onclick = () => {
        if (currentIdx > 0) {
            currentIdx--;
            renderQuestion();
        }
    };

    nextBtn.onclick = () => {
        if (currentIdx < currentQuestions.length - 1) {
            currentIdx++;
            renderQuestion();
        }
    };

    // 6. Finalize Logic
    submitBtn.onclick = () => {
        const unanswered = currentQuestions.length - Object.keys(selectedAnswers).length;
        Swal.fire({
            title: unanswered > 0 ? 'Incomplete Assessment' : 'Finish Assessment',
            text: unanswered > 0 
                ? `You have ${unanswered} questions left. Continue anyway?` 
                : 'Are you sure you want to submit your answers now?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Submit',
            cancelButtonText: 'No, Back to Exam',
            confirmButtonColor: '#2ca078'
        }).then(result => {
            if (result.isConfirmed) finalizeExam();
        });
    };

    function finalizeExam() {
        clearInterval(timerInterval);
        const formData = new FormData();
        formData.append('action', 'submit_exam');
        formData.append('applicant_id', applicantId);
        formData.append('answers', JSON.stringify(selectedAnswers));

        Swal.fire({
            title: 'Submitting...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('backend/applicant_action.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    title: 'Assessment Complete',
                    text: `Thank you. Your assessment score is ${res.score} / 15.`,
                    icon: 'success',
                    confirmButtonText: 'Close Window',
                    confirmButtonColor: '#2ca078'
                }).then(() => {
                    if (document.exitFullscreen) document.exitFullscreen();
                    window.close();
                    // Fallback if window.close() fails
                    setTimeout(() => {
                        window.location.href = 'applicationmgt.php';
                    }, 1000);
                });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        });
    }

    function autoSubmit() {
        Swal.fire({
            title: 'Time is up!',
            text: 'Your answers are being submitted automatically.',
            icon: 'info',
            timer: 3000,
            showConfirmButton: false,
            allowOutsideClick: false,
            didClose: () => { finalizeExam(); }
        });
    }

    // Prevent random unloads
    window.addEventListener('beforeunload', (e) => {
        if (timeLeft > 0 && currentQuestions.length > 0 && Object.keys(selectedAnswers).length < currentQuestions.length) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
