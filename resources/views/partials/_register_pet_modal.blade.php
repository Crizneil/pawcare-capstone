<div class="modal fade" id="registerPetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center">
                    <div class="bg-orange-subtle p-2 rounded-3 me-3">
                        <i data-lucide="paw-print" class="text-orange"></i>
                    </div>
                    <h5 class="fw-bold text-dark m-0">Pet Registration</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <form action="{{ route('pet-owner.pets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">Pet Name</label>
                        <input type="text" name="name" class="form-control rounded-pill bg-light border-0 px-3"
                            placeholder="Buddy" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="small fw-bold text-muted mb-1">Species</label>
                            <select name="species" id="speciesSelect"
                                class="form-select rounded-pill bg-light border-0 px-3" required>
                                <option value="Dog">Dog</option>
                                <option value="Cat">Cat</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted mb-1">Gender</label>
                            <select name="gender" class="form-select rounded-pill bg-light border-0 px-3" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3" id="breedSection">
                        <label class="small fw-bold text-muted mb-2 d-block">Breed</label>

                        {{-- Dog Breeds --}}
                        <div id="dogBreeds" class="breed-group">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['Aspin (PH)', 'Shih Tzu', 'Golden Retriever', 'Poodle', 'Bulldog'] as $breed)
                                    <div class="form-check form-check-inline m-0">
                                        <input class="btn-check breed-radio" type="radio" name="breed_option"
                                            id="dog_{{ Str::slug($breed) }}" value="{{ $breed }}">
                                        <label class="btn btn-outline-orange btn-sm rounded-pill px-3"
                                            for="dog_{{ Str::slug($breed) }}">{{ $breed }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Cat Breeds --}}
                        <div id="catBreeds" class="breed-group" style="display: none;">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['Puspin (PH)', 'Persian', 'Siamese', 'Maine Coon'] as $breed)
                                    <div class="form-check form-check-inline m-0">
                                        <input class="btn-check breed-radio" type="radio" name="breed_option"
                                            id="cat_{{ Str::slug($breed) }}" value="{{ $breed }}">
                                        <label class="btn btn-outline-orange btn-sm rounded-pill px-3"
                                            for="cat_{{ Str::slug($breed) }}">{{ $breed }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Others/Manual Input --}}
                        <div class="mt-2">
                            <div class="form-check form-check-inline m-0 mb-2">
                                <input class="btn-check breed-radio" type="radio" name="breed_option" id="breed_others"
                                    value="Others">
                                <label class="btn btn-outline-orange btn-sm rounded-pill px-3" for="breed_others">Others
                                    / Manual Entry</label>
                            </div>
                            <div id="manualBreedEntry" style="display: none;">
                                <input type="text" id="manualBreedInput"
                                    class="form-control rounded-pill bg-light border-0 px-3 mt-1"
                                    placeholder="Type breed here...">
                            </div>
                        </div>

                        {{-- Final breed value sent to controller --}}
                        <input type="hidden" name="breed" id="finalBreedValue" required>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-muted mb-1">Birthdate</label>
                        <input type="date" name="birthday" class="form-control rounded-pill bg-light border-0 px-3"
                            required>
                    </div>

                    <button type="submit"
                        class="btn btn-orange w-100 rounded-pill fw-bold py-3 shadow-sm transition-all">
                        CONFIRM REGISTRATION
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const speciesSelect = document.getElementById('speciesSelect');
        const dogBreeds = document.getElementById('dogBreeds');
        const catBreeds = document.getElementById('catBreeds');
        const manualBreedEntry = document.getElementById('manualBreedEntry');
        const manualBreedInput = document.getElementById('manualBreedInput');
        const finalBreedValue = document.getElementById('finalBreedValue');
        const breedRadios = document.querySelectorAll('.breed-radio');

        function updateBreedUI() {
            const species = speciesSelect.value;
            dogBreeds.style.display = (species === 'Dog') ? 'block' : 'none';
            catBreeds.style.display = (species === 'Cat') ? 'block' : 'none';

            // Reset manual entry if switching species
            if (species === 'Others') {
                manualBreedEntry.style.display = 'block';
                manualBreedInput.required = true;
                // Uncheck radios
                breedRadios.forEach(r => r.checked = false);
            } else {
                // Check if "Others" radio is checked
                const othersChecked = document.getElementById('breed_others').checked;
                manualBreedEntry.style.display = othersChecked ? 'block' : 'none';
                manualBreedInput.required = othersChecked;
            }
        }

        speciesSelect.addEventListener('change', updateBreedUI);

        breedRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.id === 'breed_others') {
                    manualBreedEntry.style.display = 'block';
                    manualBreedInput.required = true;
                    manualBreedInput.focus();
                    finalBreedValue.value = manualBreedInput.value;
                } else {
                    manualBreedEntry.style.display = 'none';
                    manualBreedInput.required = false;
                    finalBreedValue.value = this.value;
                }
            });
        });

        manualBreedInput.addEventListener('input', function () {
            if (document.getElementById('breed_others').checked || speciesSelect.value === 'Others') {
                finalBreedValue.value = this.value;
            }
        });

        // Form submission check
        document.querySelector('#registerPetModal form').addEventListener('submit', function (e) {
            if (!finalBreedValue.value) {
                e.preventDefault();
                alert('Please select or type a breed.');
            }
        });
    });
</script>