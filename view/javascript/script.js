const questionForm = document.getElementById('questionForm');
const questionsContainer = document.getElementById('questionsContainer');

let questions = JSON.parse(localStorage.getItem('gamingQA')) || [];

// Afficher les questions
function renderQuestions() {
  questionsContainer.innerHTML = '';
  questions.forEach((q, qIndex) => {
    const qCard = document.createElement('div');
    qCard.className = 'question-card';

    qCard.innerHTML = `
      <div class="question-header">
        <div class="question-title">${q.title}</div>
        <div class="question-author">par ${q.author}</div>
      </div>
      <div class="question-content">${q.content}</div>
      <button class="reply-btn" onclick="toggleReplyForm(${qIndex})">Répondre</button>

      <div class="reply-form" id="replyForm-${qIndex}">
        <input type="text" placeholder="Ton pseudo" id="replyAuthor-${qIndex}" required />
        <textarea placeholder="Ta réponse..." id="replyContent-${qIndex}" required></textarea>
        <button onclick="submitReply(${qIndex})">Envoyer</button>
      </div>

      <div class="answers">
        ${q.answers.map((a, aIndex) => `
          <div class="answer">
            <div class="answer-header">
              <span>${a.author}</span>
              <span>${new Date(a.date).toLocaleString('fr-FR')}</span>
            </div>
            <div class="answer-content">${a.content}</div>
          </div>
        `).join('')}
      </div>
    `;

    questionsContainer.appendChild(qCard);
  });
}

// Toggle formulaire réponse
function toggleReplyForm(index) {
  const form = document.getElementById(`replyForm-${index}`);
  form.style.display = form.style.display === 'block' ? 'none' : 'block';
}

// Soumettre une réponse
function submitReply(qIndex) {
  const author = document.getElementById(`replyAuthor-${qIndex}`).value.trim();
  const content = document.getElementById(`replyContent-${qIndex}`).value.trim();

  if (!author || !content) return alert("Remplis tous les champs !");

  questions[qIndex].answers.push({
    author,
    content,
    date: new Date().toISOString()
  });

  saveAndRender();
  document.getElementById(`replyForm-${qIndex}`).style.display = 'none';
  document.getElementById(`replyAuthor-${qIndex}`).value = '';
  document.getElementById(`replyContent-${qIndex}`).value = '';
}

// Soumettre une question
questionForm.addEventListener('submit', (e) => {
  e.preventDefault();
  const author = document.getElementById('author').value.trim();
  const title = document.getElementById('title').value.trim();
  const content = document.getElementById('content').value.trim();

  if (!author || !title || !content) return alert("Tous les champs sont requis !");

  questions.push({
    author,
    title,
    content,
    date: new Date().toISOString(),
    answers: []
  });

  saveAndRender();
  questionForm.reset();
});

// Sauvegarde + rendu
function saveAndRender() {
  localStorage.setItem('gamingQA', JSON.stringify(questions));
  renderQuestions();
}

// Initialisation
renderQuestions();