document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('users')) {
        users = JSON.parse(localStorage.getItem('users'));
    }
});

let users = {};

function showLogin() {
    document.getElementById('login-page').style.display = 'block';
    document.getElementById('register-page').style.display = 'none';
    document.getElementById('note-page').style.display = 'none';
}

function showRegister() {
    document.getElementById('login-page').style.display = 'none';
    document.getElementById('register-page').style.display = 'block';
}

function login() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    if (users[username] === password) {
        document.getElementById('login-page').style.display = 'none';
        document.getElementById('register-page').style.display = 'none';
        document.getElementById('note-page').style.display = 'block';
        loadNotes(username);
    } else {
        alert('Kullanıcı adı veya şifre hatalı!');
    }
}

function register() {
    const username = document.getElementById('register-username').value;
    const password = document.getElementById('register-password').value;

    if (users[username]) {
        alert('Bu kullanıcı adı zaten mevcut!');
    } else {
        users[username] = password;
        localStorage.setItem('users', JSON.stringify(users));
        alert('Kayıt başarılı!');
        showLogin();
    }
}

function saveNote() {
    const username = document.getElementById('username').value;
    const note = document.getElementById('note-input').value;

    if (!note) return;

    let notes = JSON.parse(localStorage.getItem(username)) || [];
    notes.push(note);
    localStorage.setItem(username, JSON.stringify(notes));
    document.getElementById('note-input').value = '';
    loadNotes(username);
}

function loadNotes(username) {
    const notes = JSON.parse(localStorage.getItem(username)) || [];
    const notesList = document.getElementById('notes-list');
    notesList.innerHTML = '';
    notes.forEach((note, index) => {
        const noteElement = document.createElement('div');
        noteElement.classList.add('note-item');
        noteElement.innerHTML = `
            <span>${note}</span>
            <button class="delete-btn" onclick="deleteNote(${index})">Sil</button>
        `;
        notesList.appendChild(noteElement);
    });
}

function deleteNote(index) {
    const username = document.getElementById('username').value;
    let notes = JSON.parse(localStorage.getItem(username)) || [];
    notes.splice(index, 1);
    localStorage.setItem(username, JSON.stringify(notes));
    loadNotes(username);
}

function logout() {
    document.getElementById('login-page').style.display = 'block';
    document.getElementById('register-page').style.display = 'none';
    document.getElementById('note-page').style.display = 'none';
}
