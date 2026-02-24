<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatAttachmentController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReadingActivityController;
use App\Http\Controllers\TeacherApplicationController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\LibraryCategoryController;
use App\Http\Controllers\Admin\LibraryItemController;
use App\Http\Controllers\Admin\TeacherApplicationController as AdminTeacherApplicationController;
use App\Http\Controllers\AssignmentAttachmentController;
use App\Http\Controllers\SubmissionAttachmentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\Student\JoinClassController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\ProgressController as StudentProgressController;
use App\Http\Controllers\Student\SubmissionController as StudentSubmissionController;
use App\Http\Controllers\Student\TodoController as StudentTodoController;
use App\Http\Controllers\Teacher\ClassController as TeacherClassController;
use App\Http\Controllers\Teacher\LessonController as TeacherLessonController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\SubmissionGradeController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\QuizResultController;
use App\Http\Controllers\Teacher\QuizQuestionController;
use App\Http\Controllers\Teacher\QuizAnswerReviewController;
use App\Http\Controllers\Teacher\ProgressController as TeacherProgressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/apply/teacher', [TeacherApplicationController::class, 'create'])->name('teacher-applications.create');
    Route::post('/apply/teacher', [TeacherApplicationController::class, 'store'])->name('teacher-applications.store');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:ADMIN')
        ->name('admin.dashboard');

    Route::middleware('role:ADMIN')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/teachers', [AdminTeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [AdminTeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [AdminTeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}/edit', [AdminTeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}', [AdminTeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [AdminTeacherController::class, 'destroy'])->name('teachers.destroy');

        Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [AdminStudentController::class, 'create'])->name('students.create');
        Route::post('/students', [AdminStudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [AdminStudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [AdminStudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [AdminStudentController::class, 'destroy'])->name('students.destroy');

        Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');

        Route::get('/teacher-applications', [AdminTeacherApplicationController::class, 'index'])->name('teacherApplications.index');
        Route::put('/teacher-applications/{application}/approve', [AdminTeacherApplicationController::class, 'approve'])->name('teacherApplications.approve');
        Route::put('/teacher-applications/{application}/reject', [AdminTeacherApplicationController::class, 'reject'])->name('teacherApplications.reject');

        Route::prefix('library')->name('library.')->group(function () {
            Route::get('/categories', [LibraryCategoryController::class, 'index'])->name('categories.index');
            Route::get('/categories/create', [LibraryCategoryController::class, 'create'])->name('categories.create');
            Route::post('/categories', [LibraryCategoryController::class, 'store'])->name('categories.store');
            Route::get('/categories/{category}/edit', [LibraryCategoryController::class, 'edit'])->name('categories.edit');
            Route::put('/categories/{category}', [LibraryCategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [LibraryCategoryController::class, 'destroy'])->name('categories.destroy');

            Route::get('/items', [LibraryItemController::class, 'index'])->name('items.index');
            Route::get('/items/create', [LibraryItemController::class, 'create'])->name('items.create');
            Route::post('/items', [LibraryItemController::class, 'store'])->name('items.store');
            Route::get('/items/{item}/edit', [LibraryItemController::class, 'edit'])->name('items.edit');
            Route::put('/items/{item}', [LibraryItemController::class, 'update'])->name('items.update');
            Route::delete('/items/{item}', [LibraryItemController::class, 'destroy'])->name('items.destroy');
        });
    });

    Route::get('/teacher/dashboard', [DashboardController::class, 'teacher'])
        ->middleware('role:TEACHER')
        ->name('teacher.dashboard');

    Route::get('/student/dashboard', [DashboardController::class, 'student'])
        ->middleware('role:STUDENT')
        ->name('student.dashboard');

    Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
    Route::get('/library/{item}', [LibraryController::class, 'show'])->name('library.show');
    Route::post('/library/{item}/activities', [ReadingActivityController::class, 'store'])
        ->middleware('role:STUDENT')
        ->name('library.activities.store');

    // Text-to-Speech API routes
    Route::post('/api/tts/synthesize', [App\Http\Controllers\TextToSpeechController::class, 'synthesize'])
        ->name('tts.synthesize');
    Route::get('/api/tts/voices', [App\Http\Controllers\TextToSpeechController::class, 'getVoices'])
        ->name('tts.voices');

    // Reading Assessment routes (for students)
    Route::prefix('reading-assessment')->name('reading-assessment.')->group(function () {
        Route::get('/', [App\Http\Controllers\ReadingAssessmentController::class, 'index'])->name('index');
        Route::get('/passages/{passage}', [App\Http\Controllers\ReadingAssessmentController::class, 'show'])->name('show');
        Route::post('/passages/{passage}/record', [App\Http\Controllers\ReadingAssessmentController::class, 'store'])->name('store');
        Route::get('/assessments/{assessment}/results', [App\Http\Controllers\ReadingAssessmentController::class, 'results'])->name('results');
        Route::get('/assessments/{assessment}/status', [App\Http\Controllers\ReadingAssessmentController::class, 'status'])->name('status');
        Route::get('/history', [App\Http\Controllers\ReadingAssessmentController::class, 'history'])->name('history');
        Route::delete('/assessments/{assessment}', [App\Http\Controllers\ReadingAssessmentController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('role:TEACHER')->prefix('teacher')->name('teacher.')->group(function () {
        // Reading Passage Management (for teachers)
        Route::prefix('passages')->name('passages.')->group(function () {
            Route::get('/', [App\Http\Controllers\ReadingPassageController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\ReadingPassageController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\ReadingPassageController::class, 'store'])->name('store');
            Route::get('/{passage}', [App\Http\Controllers\ReadingPassageController::class, 'show'])->name('show');
            Route::get('/{passage}/edit', [App\Http\Controllers\ReadingPassageController::class, 'edit'])->name('edit');
            Route::put('/{passage}', [App\Http\Controllers\ReadingPassageController::class, 'update'])->name('update');
            Route::delete('/{passage}', [App\Http\Controllers\ReadingPassageController::class, 'destroy'])->name('destroy');
            Route::post('/{passage}/toggle-status', [App\Http\Controllers\ReadingPassageController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{passage}/duplicate', [App\Http\Controllers\ReadingPassageController::class, 'duplicate'])->name('duplicate');
        });

        Route::get('/progress', [TeacherProgressController::class, 'index'])->name('progress.index');
        Route::get('/classes/{class}/progress', [TeacherProgressController::class, 'show'])->name('progress.class');

        Route::get('/classes', [TeacherClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/create', [TeacherClassController::class, 'create'])->name('classes.create');
        Route::post('/classes', [TeacherClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}', [TeacherClassController::class, 'show'])->name('classes.show');
        Route::get('/classes/{class}/edit', [TeacherClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{class}', [TeacherClassController::class, 'update'])->name('classes.update');
        Route::post('/classes/{class}/regenerate-code', [TeacherClassController::class, 'regenerateCode'])
            ->name('classes.regenerateCode');

        Route::post('/classes/{class}/students/add', [TeacherClassController::class, 'addStudent'])
            ->name('classes.students.add');
        Route::put('/classes/{class}/requests/{student}/approve', [TeacherClassController::class, 'approveRequest'])
            ->name('classes.requests.approve');
        Route::put('/classes/{class}/requests/{student}/reject', [TeacherClassController::class, 'rejectRequest'])
            ->name('classes.requests.reject');

        Route::get('/classes/{class}/lessons', [TeacherLessonController::class, 'index'])->name('lessons.index');
        Route::get('/classes/{class}/lessons/create', [TeacherLessonController::class, 'create'])->name('lessons.create');
        Route::post('/classes/{class}/lessons', [TeacherLessonController::class, 'store'])->name('lessons.store');
        Route::get('/classes/{class}/lessons/{lesson}', [TeacherLessonController::class, 'show'])->name('lessons.show');
        Route::delete('/classes/{class}/lessons/{lesson}', [TeacherLessonController::class, 'destroy'])->name('lessons.destroy');

        Route::post('/classes/{class}/lessons/{lesson}/materials', [MaterialController::class, 'store'])->name('materials.store');

        Route::get('/classes/{class}/assignments', [TeacherAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/classes/{class}/assignments/create', [TeacherAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/classes/{class}/assignments', [TeacherAssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/classes/{class}/assignments/{assignment}', [TeacherAssignmentController::class, 'show'])->name('assignments.show');
        Route::delete('/classes/{class}/assignments/{assignment}', [TeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');

        Route::put('/classes/{class}/assignments/{assignment}/submissions/{submission}', [SubmissionGradeController::class, 'update'])
            ->name('submissions.grade');

        Route::get('/classes/{class}/quizzes', [TeacherQuizController::class, 'index'])->name('quizzes.index');
        Route::get('/classes/{class}/quizzes/create', [TeacherQuizController::class, 'create'])->name('quizzes.create');
        Route::post('/classes/{class}/quizzes', [TeacherQuizController::class, 'store'])->name('quizzes.store');
        Route::get('/classes/{class}/quizzes/{quiz}', [TeacherQuizController::class, 'show'])->name('quizzes.show');
        Route::delete('/classes/{class}/quizzes/{quiz}', [TeacherQuizController::class, 'destroy'])->name('quizzes.destroy');
        Route::put('/classes/{class}/quizzes/{quiz}/publish', [TeacherQuizController::class, 'publish'])->name('quizzes.publish');
        Route::get('/classes/{class}/quizzes/{quiz}/results', [QuizResultController::class, 'show'])->name('quizzes.results');

        Route::post('/classes/{class}/quizzes/{quiz}/questions', [QuizQuestionController::class, 'store'])->name('quizQuestions.store');
        Route::delete('/classes/{class}/quizzes/{quiz}/questions/{question}', [QuizQuestionController::class, 'destroy'])->name('quizQuestions.destroy');

        Route::put('/classes/{class}/quizzes/{quiz}/questions/{question}/answers/{answer}', [QuizAnswerReviewController::class, 'update'])
            ->name('quizAnswers.review');
    });

    Route::middleware('role:STUDENT')->prefix('student')->name('student.')->group(function () {
        Route::get('/progress', [StudentProgressController::class, 'index'])->name('progress.index');
        Route::get('/classes/{class}/progress', [StudentProgressController::class, 'show'])->name('progress.class');

        Route::get('/todo', [StudentTodoController::class, 'index'])->name('todo.index');

        Route::get('/classes', [JoinClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/join', [JoinClassController::class, 'create'])->name('classes.join');
        Route::post('/classes/join', [JoinClassController::class, 'store'])->name('classes.join.store');

        Route::get('/classes/{class}/lessons', [StudentLessonController::class, 'index'])->name('lessons.index');
        Route::get('/classes/{class}/lessons/{lesson}', [StudentLessonController::class, 'show'])->name('lessons.show');

        Route::get('/classes/{class}/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/classes/{class}/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
        Route::post('/classes/{class}/assignments/{assignment}/submit', [StudentSubmissionController::class, 'store'])->name('submissions.store');
        Route::post('/classes/{class}/assignments/{assignment}/unsubmit', [StudentSubmissionController::class, 'unsubmit'])->name('submissions.unsubmit');

        Route::get('/classes/{class}/quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');
        Route::get('/classes/{class}/quizzes/{quiz}/take', [StudentQuizController::class, 'take'])->name('quizzes.take');
        Route::post('/classes/{class}/quizzes/{quiz}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
        Route::get('/classes/{class}/quizzes/{quiz}/result', [StudentQuizController::class, 'result'])->name('quizzes.result');
    });

    Route::get('/classes/{class}/assignments/{assignment}/attachment', [AssignmentAttachmentController::class, 'show'])
        ->name('assignments.attachment');

    Route::get('/classes/{class}/assignments/{assignment}/attachment/preview', [AssignmentAttachmentController::class, 'preview'])
        ->name('assignments.attachment.preview');

    Route::get('/classes/{class}/assignments/{assignment}/submissions/{submission}/attachment', [SubmissionAttachmentController::class, 'stream'])
        ->name('submissions.attachment');

    Route::get('/classes/{class}/assignments/{assignment}/submissions/{submission}/attachment/preview', [SubmissionAttachmentController::class, 'preview'])
        ->name('submissions.attachment.preview');

    Route::get('/materials/{material}/preview', [MaterialController::class, 'preview'])->name('materials.preview');
    Route::get('/materials/{material}/stream', [MaterialController::class, 'stream'])->name('materials.stream');

    Route::get('/classes/{class}/chat', [ChatController::class, 'show'])->name('classes.chat.show');
    Route::post('/classes/{class}/chat', [ChatController::class, 'store'])->name('classes.chat.store');
    Route::get('/classes/{class}/chat/messages/{message}/attachment', [ChatAttachmentController::class, 'show'])->name('classes.chat.attachment');

    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/search/api', [SearchController::class, 'api'])->name('search.api');
    Route::get('/users/{user}', [UserProfileController::class, 'show'])->name('users.show');

    Route::get('/messages', [DirectMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [DirectMessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [DirectMessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}/attachments/{message}', [DirectMessageAttachmentController::class, 'show'])->name('messages.attachment');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
