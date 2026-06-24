
function toggleGameSelect() {
    const category = document.getElementById('category').value;
    const gameSelectGroup = document.getElementById('game-select-group');
    const gameIdSelect = document.getElementById('game_id');

    if (category === 'games') {
        gameSelectGroup.style.display = 'block';
        gameIdSelect.required = true;
    } else {
        gameSelectGroup.style.display = 'none';
        gameIdSelect.required = false;
        gameIdSelect.value = '';
    }
}
