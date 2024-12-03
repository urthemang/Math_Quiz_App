<!DOCTYPE html>
<html>
<head>
    <title>Math Quiz Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .quiz-container {
            max-width: 500px;
            margin: auto;
            text-align: center;
        }
        .settings, .quiz {
            display: none;
        }
        .settings.active, .quiz.active {
            display: block;
        }
        button {
            margin: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <h1>Math Quiz</h1>
       
        <div class="settings active" id="settings">
            <h2>Settings</h2>
           
            <form id="settingsForm">
                <fieldset>
                    <legend>Level</legend>
                    <label><input type="radio" name="level" value="1" required> Level 1 (1-10)</label><br>
                    <label><input type="radio" name="level" value="2"> Level 2 (11-100)</label><br>
                    <label><input type="radio" name="level" value="custom"> Custom Level</label><br>
                    <input type="number" name="min" placeholder="Min" min="1"> -
                    <input type="number" name="max" placeholder="Max" min="1">
                </fieldset>

                <fieldset>
                    <legend>Operator</legend>
                    <label><input type="radio" name="operator" value="add" required> Addition</label><br>
                    <label><input type="radio" name="operator" value="subtract"> Subtraction</label><br>
                    <label><input type="radio" name="operator" value="multiply"> Multiplication</label>
                </fieldset>

                <fieldset>
                    <legend>Quiz Settings</legend>
                    <label>Number of Questions:
                        <input type="number" name="questions" value="10" min="1" required>
                    </label><br>
                    <label>Max Difference of Choices:
                        <input type="number" name="choiceRange" value="20" min="1" required>
                    </label>
                </fieldset>

                <button type="button" onclick="startQuiz()">Start Quiz</button>
            </form>
        </div>
                       
       
        <div class="quiz" id="quiz">
            <h2>Question <span id="currentQuestion">1</span></h2>
            <p id="question"></p>
            <div id="options">
                
            </div>
            <button type="button" onclick="nextQuestion()">Next</button>
        </div>

      
        <div id="results" style="display:none;">
            <h2>Quiz Results</h2>
            <p>Correct: <span id="correctAnswers"></span></p>
            <p>Wrong: <span id="wrongAnswers"></span></p>
            <p>Remarks: <span id="remarks"></span></p>
            <button type="button" onclick="resetQuiz()">Reset Quiz</button>
        </div>
    </div>

    <script>
        let quizData = [];
        let currentQuestionIndex = 0;
        let correctAnswers = 0;
        let wrongAnswers = 0;

        function startQuiz() {
            const form = document.getElementById('settingsForm');
            const formData = new FormData(form);
            const level = formData.get('level');
            const operator = formData.get('operator');
            const questions = parseInt(formData.get('questions'));
            const choiceRange = parseInt(formData.get('choiceRange'));
            let min = 1, max = 10;

            if (level === '2') {
                min = 11; max = 100;
            } else if (level === 'custom') {
                min = parseInt(formData.get('min'));
                max = parseInt(formData.get('max'));
            }

            quizData = generateQuiz(min, max, operator, questions, choiceRange);
            document.getElementById('settings').classList.remove('active');
            document.getElementById('quiz').classList.add('active');
            showQuestion();
        }

        function generateQuiz(min, max, operator, questions, choiceRange) {
            const quiz = [];
            for (let i = 0; i < questions; i++) {
                const num1 = Math.floor(Math.random() * (max - min + 1)) + min;
                const num2 = Math.floor(Math.random() * (max - min + 1)) + min;
                let question, correctAnswer;

                switch (operator) {
                    case 'add':
                        question = `${num1} + ${num2}`;
                        correctAnswer = num1 + num2;
                        break;
                    case 'subtract':
                        question = `${num1} - ${num2}`;
                        correctAnswer = num1 - num2;
                        break;
                    case 'multiply':
                        question = `${num1} * ${num2}`;
                        correctAnswer = num1 * num2;
                        break;
                }

                const choices = generateChoices(correctAnswer, choiceRange);
                quiz.push({ question, correctAnswer, choices });
            }
            return quiz;
        }

        function generateChoices(correctAnswer, range) {
            const choices = new Set();
            choices.add(correctAnswer);

            while (choices.size < 4) {
                const choice = correctAnswer + Math.floor(Math.random() * (2 * range + 1)) - range;
                if (choice !== correctAnswer) choices.add(choice);
            }

            return Array.from(choices).sort(() => Math.random() - 0.5);
        }

        function showQuestion() {
            const questionData = quizData[currentQuestionIndex];
            document.getElementById('currentQuestion').textContent = currentQuestionIndex + 1;
            document.getElementById('question').textContent = questionData.question;

            const optionsDiv = document.getElementById('options');
            optionsDiv.innerHTML = '';

            questionData.choices.forEach(choice => {
                const button = document.createElement('button');
                button.textContent = choice;
                button.onclick = () => checkAnswer(choice);
                optionsDiv.appendChild(button);
            });
        }

        function checkAnswer(choice) {
            const questionData = quizData[currentQuestionIndex];
            if (choice === questionData.correctAnswer) {
                correctAnswers++;
            } else {
                wrongAnswers++;
            }
            nextQuestion();
        }

        function nextQuestion() {
            currentQuestionIndex++;
            if (currentQuestionIndex < quizData.length) {
                showQuestion();
            } else {
                endQuiz();
            }
        }

     function endQuiz() {
    document.getElementById('quiz').classList.remove('active');
    document.getElementById('results').style.display = 'block';

    document.getElementById('correctAnswers').textContent = correctAnswers;
    document.getElementById('wrongAnswers').textContent = wrongAnswers;
    document.getElementById('remarks').textContent = correctAnswers > wrongAnswers ? 'Well done!' : 'Better luck next time!';

    const username = prompt("Enter your name to save your result:");
    if (username) {
        const data = {
            username: username,
            correct_answers: correctAnswers,
            wrong_answers: wrongAnswers
        };

        fetch('save_results.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            alert(result.message); 
        })
        .catch(error => {
            console.error('Error saving results:', error);
        });
    }
}


        function resetQuiz() {
            currentQuestionIndex = 0;
            correctAnswers = 0;
            wrongAnswers = 0;
            document.getElementById('results').style.display = 'none';
            document.getElementById('settings').classList.add('active');
        }
    </script>
</body>
</html>
