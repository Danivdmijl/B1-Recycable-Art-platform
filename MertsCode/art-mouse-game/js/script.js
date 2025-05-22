const mouse = document.getElementById('mouse');
const art = document.getElementById('art');
const scoreEl = document.getElementById('score');
const levelEl = document.getElementById('level');
const timerEl = document.getElementById('timer');
const gameArea = document.getElementById('gameArea');

let score = 0;
let level = 1;
let speed = 20;
let posX = 50, posY = 50;
let timeLeft = 60;
let gameInterval;
let moveInterval;
let gameOver = false;
let direction = null;
let mouseSize = 80;

function moveArt() {
  const x = Math.random() * (gameArea.clientWidth - art.clientWidth);
  const y = Math.random() * (gameArea.clientHeight - art.clientHeight);
  art.style.left = `${x}px`;
  art.style.top = `${y}px`;
}

function checkCollision() {
  const mouseRect = mouse.getBoundingClientRect();
  const artRect = art.getBoundingClientRect();
  const gameRect = gameArea.getBoundingClientRect();

  const mouseX = mouseRect.left - gameRect.left;
  const mouseY = mouseRect.top - gameRect.top;
  const artX = artRect.left - gameRect.left;
  const artY = artRect.top - gameRect.top;

  const overlap = !(
    mouseX + mouse.clientWidth < artX ||
    mouseX > artX + art.clientWidth ||
    mouseY + mouse.clientHeight < artY ||
    mouseY > artY + art.clientHeight
  );

  if (overlap) {
    score++;
    scoreEl.textContent = score;
    moveArt();
    mouseSize += 5;
    mouse.style.width = `${mouseSize}px`;

    if (score % 5 === 0) {
      level++;
      levelEl.textContent = level;
      speed += 2;
      restartMovement();
    }
  }
}

function checkBorders() {
  if (
    posX < 0 ||
    posY < 0 ||
    posX > gameArea.clientWidth - mouse.clientWidth ||
    posY > gameArea.clientHeight - mouse.clientHeight
  ) {
    endGame("You hit the wall! Game over.\nScore: " + score);
  }
}

function moveMouseAutomatically() {
  if (gameOver || !direction) return;

  switch (direction) {
    case 'ArrowUp': posY -= speed; break;
    case 'ArrowDown': posY += speed; break;
    case 'ArrowLeft': posX -= speed; break;
    case 'ArrowRight': posX += speed; break;
  }

  mouse.style.left = `${posX}px`;
  mouse.style.top = `${posY}px`;

  checkBorders();
  checkCollision();
}

function startMovement() {
  moveInterval = setInterval(moveMouseAutomatically, 200);
}

function restartMovement() {
  clearInterval(moveInterval);
  startMovement();
}

function endGame(message) {
  clearInterval(gameInterval);
  clearInterval(moveInterval);
  gameOver = true;
  alert(message);
  resetGame();
}

document.addEventListener('keydown', (e) => {
  const allowed = ['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'];
  if (allowed.includes(e.key)) {
    e.preventDefault();
    if (gameOver) return;
    direction = e.key;
    if (!moveInterval) startMovement();
  }
});

function startTimer() {
  gameInterval = setInterval(() => {
    timeLeft--;
    timerEl.textContent = timeLeft;

    if (timeLeft <= 0) {
      endGame("Time's up! Your score is: " + score);
    }
  }, 1000);
}

function resetGame() {
  score = 0;
  level = 1;
  speed = 20;
  posX = 50;
  posY = 50;
  timeLeft = 60;
  gameOver = false;
  direction = null;
  mouseSize = 80;

  scoreEl.textContent = score;
  levelEl.textContent = level;
  timerEl.textContent = timeLeft;

  mouse.style.left = `${posX}px`;
  mouse.style.top = `${posY}px`;
  mouse.style.width = `${mouseSize}px`;

  moveArt();
  startTimer();
}

moveArt();
startTimer();
