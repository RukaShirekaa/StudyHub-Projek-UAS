<?php require_once __DIR__ . '/../layout/header.php'; ?>

<?php
    $score = round($result['score']);
    $correct = round(($result['score']/100)*$result['total_questions']);
    $total = $result['total_questions'];
    $scoreColor = $score >= 70 ? 'var(--accent-emerald)' : ($score >= 40 ? 'var(--accent-amber)' : 'var(--accent-rose)');
    $emoji = $score >= 80 ? '🎉' : ($score >= 60 ? '👍' : ($score >= 40 ? '💪' : '📖'));
    $msg = $score >= 80 ? 'Luar biasa! Kamu menguasai materi ini.' : ($score >= 60 ? 'Bagus! Tinggal sedikit lagi.' : ($score >= 40 ? 'Lumayan, terus belajar ya!' : 'Jangan menyerah, coba review materinya.'));
?>

<!-- Score Card -->
<div class="card" style="text-align:center; margin-bottom:1.5rem; padding: 2.5rem 1.5rem;">
    <h3 class="section-title no-line" style="justify-content: center; margin-bottom: 1.5rem;">
        <i class="fa-solid fa-trophy" style="color: var(--accent-amber);"></i> 
        Hasil Quiz: <?= htmlspecialchars($result['title']) ?>
    </h3>

    <!-- Score Circle -->
    <div class="score-circle" style="margin-bottom: 1.5rem;">
        <svg width="140" height="140" viewBox="0 0 140 140">
            <circle cx="70" cy="70" r="60" fill="none" stroke="var(--border-color)" stroke-width="10"/>
            <circle cx="70" cy="70" r="60" fill="none" stroke="<?= $scoreColor ?>" stroke-width="10"
                stroke-dasharray="<?= 2 * 3.14159 * 60 ?>" 
                stroke-dashoffset="<?= 2 * 3.14159 * 60 * (1 - $score/100) ?>"
                stroke-linecap="round"
                style="transition: stroke-dashoffset 1.5s ease;"/>
        </svg>
        <span class="score-value" style="color: <?= $scoreColor ?>;" data-counter="<?= $score ?>"><?= $score ?></span>
    </div>

    <div style="font-size: 2rem; margin-bottom: 0.5rem;"><?= $emoji ?></div>
    <p style="color:var(--text-muted); font-size: 1rem; margin-bottom: 0.25rem;"><?= $msg ?></p>
    <p style="color:var(--text-muted); font-size: 0.9rem;">
        Benar <strong style="color: var(--accent-emerald);"><?= $correct ?></strong> dari <strong><?= $total ?></strong> soal
    </p>

    <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>/quiz" class="btn btn-primary"><i class="fa-solid fa-rotate-right"></i> Coba Quiz Lain</a>
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline"><i class="fa-solid fa-house"></i> Beranda</a>
    </div>
</div>

<!-- Review Section -->
<div class="card">
    <h3 class="section-title"><i class="fa-solid fa-magnifying-glass" style="color: var(--secondary);"></i> Pembahasan Singkat</h3>
    <?php foreach($questions as $index => $q): ?>
        <div class="question-card animate-in">
            <div style="display: flex; align-items: flex-start; margin-bottom: 0.75rem;">
                <span class="question-number"><?= ($index+1) ?></span>
                <p style="font-weight: 600; font-size: 0.95rem; line-height: 1.5;"><?= htmlspecialchars($q['question']) ?></p>
            </div>
            <div style="padding-left: 2.75rem;">
                <?php
                $userAns = $user_answers[$q['id']] ?? null;
                $isCorrect = ($userAns === $q['correct_answer']);
                ?>
                
                <?php if ($userAns): ?>
                    <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <?php if ($isCorrect): ?>
                            <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.4rem 0.75rem;"><i class="fa-solid fa-check"></i> Jawabanmu Benar</span>
                        <?php else: ?>
                            <span class="badge" style="background: rgba(244, 63, 94, 0.15); color: var(--accent-rose); border: 1px solid rgba(244, 63, 94, 0.3); padding: 0.4rem 0.75rem;"><i class="fa-solid fa-xmark"></i> Jawabanmu Salah</span>
                            <span style="font-weight: 500; color: var(--accent-rose); text-decoration: line-through; font-size: 0.9rem;">
                                <?= htmlspecialchars($userAns) ?>. <?= htmlspecialchars($q['option_'.strtolower($userAns)]) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: var(--accent-amber); border: 1px solid rgba(245, 158, 11, 0.3); padding: 0.4rem 0.75rem;"><i class="fa-solid fa-circle-exclamation"></i> Tidak Dijawab</span>
                    </div>
                <?php endif; ?>

                <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.4rem 0.75rem;"><i class="fa-solid fa-key"></i> Kunci Jawaban</span>
                    <span style="font-weight: 600; color: var(--accent-emerald); font-size: 0.95rem;"><?= htmlspecialchars($q['correct_answer']) ?>. <?= htmlspecialchars($q['option_'.strtolower($q['correct_answer'])]) ?></span>
                </div>
                
                <p style="color:var(--text-muted); font-size: 0.875rem; line-height: 1.6; background: rgba(99, 102, 241, 0.04); padding: 0.75rem 1rem; border-radius: var(--radius-md); border-left: 3px solid var(--primary);">
                    <strong>Penjelasan:</strong> <?= htmlspecialchars($q['explanation']) ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
