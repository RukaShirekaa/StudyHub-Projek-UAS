<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.25rem;">Latihan & Quiz</h1>
    <p style="color: var(--text-muted); font-size: 1rem; margin: 0;">Uji pemahamanmu dengan quiz interaktif.</p>
</div>

<!-- Mulai Latihan Baru -->
<div class="card" style="margin-bottom: 2.5rem;">
    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fa-solid fa-wand-magic-sparkles" style="color: var(--primary);"></i> Mulai Latihan Baru
    </h3>
    
    <form action="<?= BASE_URL ?>/quiz/generate" method="POST" onsubmit="document.getElementById('loading').style.display='block'; document.getElementById('quizFormContent').style.display='none';">
        <div id="quizFormContent">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                
                <div class="form-group-modern" style="margin-bottom: 0;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem; display: block;">Materi Kuliah</label>
                    <select name="material_id" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-color); color: var(--text-main);">
                        <option value="">Pilih materi...</option>
                        <?php 
                        $selected_material_id = $_GET['material_id'] ?? '';
                        foreach($materials as $m): 
                        ?>
                            <option value="<?= $m['id'] ?>" <?= $m['id'] == $selected_material_id ? 'selected' : '' ?>><?= htmlspecialchars($m['course_name']) ?> - <?= htmlspecialchars($m['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group-modern" style="margin-bottom: 0;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem; display: block;">Jumlah Soal</label>
                    <select name="total_questions" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-color); color: var(--text-main);">
                        <option value="5">5 Soal</option>
                        <option value="10">10 Soal</option>
                        <option value="15">15 Soal</option>
                        <option value="20">20 Soal</option>
                    </select>
                </div>

                <div class="form-group-modern" style="margin-bottom: 0;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem; display: block;">Tingkat Kesulitan</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <label style="flex: 1; cursor: pointer;">
                            <input type="radio" name="difficulty" value="easy" style="display: none;">
                            <div class="difficulty-btn" style="text-align: center; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-color); color: var(--text-muted); font-size: 0.9rem; font-weight: 600; transition: all 0.2s ease;">Mudah</div>
                        </label>
                        <label style="flex: 1; cursor: pointer;">
                            <input type="radio" name="difficulty" value="medium" checked style="display: none;">
                            <div class="difficulty-btn" style="text-align: center; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-color); color: var(--text-muted); font-size: 0.9rem; font-weight: 600; transition: all 0.2s ease;">Sedang</div>
                        </label>
                        <label style="flex: 1; cursor: pointer;">
                            <input type="radio" name="difficulty" value="hard" style="display: none;">
                            <div class="difficulty-btn" style="text-align: center; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-color); color: var(--text-muted); font-size: 0.9rem; font-weight: 600; transition: all 0.2s ease;">Sulit</div>
                        </label>
                    </div>
                </div>

            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2rem; width: 100%; justify-content: center;">
                Generate Quiz
            </button>
        </div>

        <div id="loading" style="display:none; text-align:center; padding: 2rem 0;">
            <div style="width: 50px; height: 50px; border: 3px solid var(--border-color); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1rem;"></div>
            <p style="color:var(--text-main); font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem;">AI sedang membuat quiz...</p>
            <p style="color:var(--text-muted); font-size: 0.85rem;">Mohon tunggu sebentar, AI sedang menganalisis materi Anda.</p>
        </div>
    </form>
</div>

<!-- Riwayat Latihan Terakhir -->
<div>
    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem;">Riwayat Latihan Terakhir</h3>
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        <?php if(empty($recentQuizzes)): ?>
            <div class="card" style="text-align: center; padding: 2rem;">
                <i class="fa-solid fa-inbox" style="font-size: 2rem; color: var(--text-muted); opacity: 0.3; margin-bottom: 1rem;"></i>
                <p style="color: var(--text-muted);">Belum ada riwayat quiz.</p>
            </div>
        <?php else: ?>
            <?php foreach($recentQuizzes as $q): ?>
                <div class="card" style="padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1.25rem; flex: 1; min-width: 250px;">
                        <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: rgba(99,102,241,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                            <i class="fa-solid fa-gamepad"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 1.05rem; margin-bottom: 0.25rem; color: var(--text-main); overflow-wrap: anywhere;">Quiz: <?= htmlspecialchars($q['material_title']) ?></div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                                <?= htmlspecialchars($q['course_name'] ?? 'Unknown Course') ?> &bull; <?= $q['total_questions'] ?? 0 ?> Soal &bull; <?= ucfirst(htmlspecialchars($q['difficulty'] ?? '')) ?>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex: 1; min-width: 200px;">
                        <div style="text-align: left;">
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Skor</div>
                            <div style="font-weight: 700; font-size: 1.1rem; color: <?= $q['score'] >= 70 ? 'var(--accent-emerald)' : 'var(--accent-rose)' ?>;">
                                <?= round($q['score']) ?>/100
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>/quiz/result?id=<?= $q['quiz_id'] ?>" class="btn btn-outline" style="padding: 0.5rem 1rem; white-space: nowrap;">Lihat Hasil</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
    input[type="radio"]:checked + .difficulty-btn {
        background: rgba(99, 102, 241, 0.1) !important;
        border-color: var(--primary) !important;
        color: var(--primary) !important;
    }
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
