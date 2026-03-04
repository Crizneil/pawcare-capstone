function initializePetBreedLogic(config) {
    const speciesSelect = document.getElementById(config.speciesId);
    const breedSelect = document.getElementById(config.breedId);
    const otherContainer = document.getElementById(config.otherContainerId);
    const otherInput = document.getElementById(config.otherInputId);

    // Safety check: stop if elements aren't found
    if (!speciesSelect || !breedSelect) return;

    const breeds = {
        'Dog': ['Golden Retriever', 'German Shepherd', 'Poodle', 'Bulldog', 'Pomeranian', 'Shih Tzu', 'Aspin', 'Chihuahua', 'Other'],
        'Cat': ['Persian', 'Siamese', 'Maine Coon', 'Bengal', 'Puspin', 'Other'],
        'Other': ['Other']
    };

    // Handle species change
    speciesSelect.addEventListener('change', function () {
        const selected = this.value;

        // 1. Reset breed dropdown
        breedSelect.innerHTML = '<option value="" selected disabled>Select Breed</option>';

        // 2. Hide "Other" input and reset it
        otherContainer.classList.add('d-none');
        otherInput.required = false;
        otherInput.value = '';

        // 3. Populate breeds for selected species
        if (breeds[selected]) {
            breeds[selected].forEach(breed => {
                const option = document.createElement('option');
                option.value = breed;
                option.textContent = breed;
                breedSelect.appendChild(option);
            });
        }

        // Auto-select "Other" if the species is "Other" to immediately open the manual input
        if (selected === 'Other') {
            breedSelect.value = 'Other';
            breedSelect.dispatchEvent(new Event('change'));
        }
    });

    // Handle breed change
    breedSelect.addEventListener('change', function () {
        if (this.value === 'Other') {
            if (otherContainer) {
                otherContainer.classList.remove('d-none');
                otherContainer.style.display = 'block'; // Force display override
            }
            if (otherInput) {
                otherInput.required = true;
                otherInput.disabled = false;    // Strip disabled if cached
                otherInput.readOnly = false;    // Strip readonly if cached
                otherInput.style.pointerEvents = 'auto'; // Force interaction

                // If species is Other, just say "Enter Breed", otherwise say "Enter Dog/Cat Breed"
                otherInput.placeholder = speciesSelect.value === 'Other' ? "Enter Breed" : "Enter " + speciesSelect.value + " Breed";

                // Blast focus directly to the input box to instantly enable typing
                setTimeout(() => {
                    otherInput.focus();
                }, 100);
            }
        } else {
            if (otherContainer) {
                otherContainer.classList.add('d-none');
                otherContainer.style.display = ''; // Reset inline style
            }
            if (otherInput) {
                otherInput.required = false;
                otherInput.value = '';
            }
        }
    });

    otherInput.addEventListener('input', function () {
        if (breedSelect.value === 'Other') {
        }
    });
}
