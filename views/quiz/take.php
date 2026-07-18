<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.25rem;">Quiz: <?= htmlspecialchars($material['title'] ?? 'Latihan') ?></h1>
        <p style="color: var(--text-muted); font-size: 1rem; margin: 0;">Pilih jawaban yang paling tepat.</p>
    </div>
    <div style="display: flex; align-items: center; gap: 0.5rem; background: var(--surface); border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: var(--radius-full); font-weight: 700; color: var(--accent-rose); font-size: 1.1rem; min-width: 100px; justify-content: center;">
        <i class="fa-regular fa-clock"></i> <span id="timerDisplay">05:00</span>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-main); font-weight: 600;" id="questionCounter">Pertanyaan 1 dari <?= count($questions) ?></h4>
        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; background: var(--bg-color); padding: 0.25rem 0.75rem; border-radius: var(--radius-full); border: 1px solid var(--border-color);" id="progressText">0% Selesai</span>
    </div>

    <div class="progress-bar" style="margin-bottom: 2rem; height: 8px; background: var(--bg-color);">
        <div class="progress-bar-fill" style="width: 0%; transition: width 0.3s ease;" id="quizProgress"></div>
    </div>
    
    <form action="<?= BASE_URL ?>/quiz/submit" method="POST" id="quizForm">
        <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
        
        <?php foreach($questions as $index => $q): ?>
        <div class="question-container" id="q_container_<?= $index ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
            
            <p style="font-weight: 600; font-size: 1.15rem; line-height: 1.6; margin-bottom: 1.5rem; color: var(--text-main);">
                <?= htmlspecialchars($q['question']) ?>
            </p>
            
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <?php foreach (['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $letter => $field): ?>
                <label class="radio-card" style="padding: 1rem 1.25rem; font-size: 1rem; display: flex; align-items: center; gap: 1rem;">
                    <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $letter ?>" onchange="updateProgress(); autoNext(<?= $index ?>);">
                    <span><strong style="color: var(--primary); margin-right: 0.5rem; font-size: 1.1rem;"><?= $letter ?>.</strong> <?= htmlspecialchars($q[$field]) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            
        </div>
        <?php endforeach; ?>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
            <button type="button" class="btn btn-outline" id="prevBtn" onclick="navQuestion(-1)" style="visibility: hidden;">
                <i class="fa-solid fa-arrow-left"></i> Sebelumnya
            </button>
            
            <button type="button" class="btn btn-primary" id="nextBtn" onclick="navQuestion(1)">
                Selanjutnya <i class="fa-solid fa-arrow-right"></i>
            </button>
            
            <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none; background: var(--accent-emerald); border-color: var(--accent-emerald);">
                <i class="fa-solid fa-check"></i> Kumpulkan
            </button>
        </div>
    </form>
</div>

<script>
    const totalQuestions = <?= count($questions) ?>;
    let currentQ = 0;
    
    function navQuestion(dir) {
        document.getElementById('q_container_' + currentQ).style.display = 'none';
        currentQ += dir;
        document.getElementById('q_container_' + currentQ).style.display = 'block';
        
        document.getElementById('questionCounter').innerText = `Pertanyaan ${currentQ + 1} dari ${totalQuestions}`;
        
        document.getElementById('prevBtn').style.visibility = currentQ > 0 ? 'visible' : 'hidden';
        
        if (currentQ === totalQuestions - 1) {
            document.getElementById('nextBtn').style.display = 'none';
            document.getElementById('submitBtn').style.display = 'inline-flex';
        } else {
            document.getElementById('nextBtn').style.display = 'inline-flex';
            document.getElementById('submitBtn').style.display = 'none';
        }
    }
    
    function autoNext(index) {
        if (index < totalQuestions - 1) {
            setTimeout(() => {
                if (currentQ === index) navQuestion(1);
            }, 400); // short delay
        }
    }

    function updateProgress() {
        const answered = document.querySelectorAll('#quizForm input[type="radio"]:checked').length;
        const pct = Math.round((answered / totalQuestions) * 100);
        document.getElementById('quizProgress').style.width = pct + '%';
        document.getElementById('progressText').innerText = pct + '% Selesai';
    }
    
    // Timer logic
    let timeLeft = 300; // 5 minutes
    const timerDisplay = document.getElementById('timerDisplay');
    const timerInterval = setInterval(() => {
        timeLeft--;
        const m = Math.floor(timeLeft / 60).toString().padStart(2, '0');
        const s = (timeLeft % 60).toString().padStart(2, '0');
        timerDisplay.innerText = `${m}:${s}`;
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            document.getElementById('quizForm').submit();
        }
    }, 1000);

    // Prevent manual submission if not all questions are answered
    document.getElementById('quizForm').addEventListener('submit', function(e) {
        const answered = document.querySelectorAll('#quizForm input[type="radio"]:checked').length;
        if (answered < totalQuestions) {
            e.preventDefault();
            showToast(`Harap jawab semua pertanyaan! Anda baru menjawab ${answered} dari ${totalQuestions}.`, 'error');
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
