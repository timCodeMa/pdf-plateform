document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.querySelector('.upload-form');
    
    if (uploadForm) {
        const progressContainer = document.createElement('div');
        progressContainer.className = 'progress-container';
        progressContainer.innerHTML = `
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
            <div class="progress-text"></div>
            <div class="progress-details"></div>
        `;
        
        uploadForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            uploadForm.appendChild(progressContainer);
            const progressBar = progressContainer.querySelector('.progress');
            const progressText = progressContainer.querySelector('.progress-text');
            const progressDetails = progressContainer.querySelector('.progress-details');
            
            try {
                const response = await fetch('upload.php', {
                    method: 'POST',
                    body: formData
                });
                
                const reader = response.body.getReader();
                
                while (true) {
                    const {value, done} = await reader.read();
                    if (done) break;
                    
                    const progress = JSON.parse(new TextDecoder().decode(value));
                    
                    progressBar.style.width = `${progress.percentage}%`;
                    progressText.textContent = `Traitement de la page ${progress.page}/${progress.total}`;
                    progressDetails.textContent = `${progress.percentage}% complété`;
                }
                
            } catch (error) {
                progressDetails.textContent = `Erreur: ${error.message}`;
            }
        });
    }
});
