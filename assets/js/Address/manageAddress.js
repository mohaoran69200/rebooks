function addAddress(event) {
    event.preventDefault();

    const list = document.querySelector('ul.space-y-6');
    const prototype = list.dataset.prototype;
    const index = list.children.length;
    const newForm = prototype.replace(/__name__/g, index);

    const newElement = document.createElement('li');
    newElement.classList.add('border', 'border-gray-300', 'p-6', 'rounded-lg', 'shadow-md', 'bg-white', 'relative');
    newElement.innerHTML = `
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Nouvelle adresse</h3>
        ${newForm}
        <button type="button" class="absolute top-3 right-3 text-red-600 hover:text-red-800 cursor-pointer font-semibold delete-address">✖</button>
    `;

    list.appendChild(newElement);
    attachDeleteListener(newElement);
}

function attachDeleteListener(element) {
    const deleteButton = element.querySelector('.delete-address');
    deleteButton.addEventListener('click', function () {
        element.remove();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-address').forEach(button => {
        attachDeleteListener(button.closest('li'));
    });

    document.querySelector('button[onclick="addAddress(event)"]').addEventListener('click', addAddress);
});
