function addNewItem() {
    let container = document.getElementById('new-item-container');
    let newItem = document.createElement('div');
    newItem.classList.add('item');

    // Create the HTML for the new item including the "Remove" button
    newItem.innerHTML = `
        <label for="position">Position:</label>
        <input type="number" name="new_position[]" required><br>
        <label for="name">Name:</label>
        <input type="text" name="new_name[]" required><br>
        <label for="link">Link:</label>
        <input type="url" name="new_link[]" required><br>
        <label for="description">Description:</label>
        <textarea name="new_description[]"></textarea><br>
        <button type="button" class="button-red" onclick="removeNewItem(this)">Remove</button><br>
    `;

    container.appendChild(newItem);
}

// Function to remove an item
function removeNewItem(button) {
    // Get the parent div (the item) and remove it from the container
    let item = button.parentElement;
    item.remove();
}

function markForDeletion(button) {
    let itemDiv = button.closest('.item'); // Get the item div

    // Add a hidden input to mark this item for deletion
    let itemId = itemDiv.getAttribute('data-item-id');
    let deleteInput = document.createElement('input');
    deleteInput.type = 'hidden';
    deleteInput.name = 'delete_item[]';
    deleteInput.value = itemId;

    // Append the hidden input to the form
    document.querySelector('form').appendChild(deleteInput);

    // Visually hide the item
    itemDiv.style.display = 'none';
}