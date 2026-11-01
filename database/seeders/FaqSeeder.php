<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bahasa = [
            [
                'question' => 'Apa itu Arabic-go?',
                'answere' => '<p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">Arabic-Go adalah sebuah platform online pembelajaran belajar bahasa arab bersertifikat. Arabic-go berawal dari sebuah proyek pemberian harokat otomatis terhadap tulisan arab gundul hasil kerjasama antara Politeknik Caltex Riau, Indonesia dan Advance Pact Sd. Bhd, Malaysia. Misi kami adalah menghadirkan pembelajaran bahasa arab yang mudah dan menarik bagi semua kalangan. Arabic-Go juga menerapkan system learning path, sehingga kamu dapat mempelajari focus ilmu bahasa arab sesuai dengan minatmu.</span></span></span></p>

            <p><span style="font-size:11.0pt"><span style="font-family:Montserrat">Untuk detail lebih lanjut, kunjungi halaman tentang kami.</span></span></p>
            '
            ],
            [
                'question' => 'Bisakah kamu menjadi fasih berbahasa arab dengan Arabic-Go?',
                'answere' => '<p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">Kamu mungkin bercita-cita untuk menjadi &ldquo;fasih&rdquo;, tetapi &quot;nyaman&rdquo; mungkin adalah apa yang akan kamu dapatkan. Kamu dapat merasa nyaman bahkan sebagai pemula, tergantung pada tujuan kamu mempelajari bahasa arab! Karena bahasa yang kamu butuhkan untuk bepergian sebagai turis selama seminggu akan jauh berbeda dengan kebutuhan bahasa seorang profesional di tempat kerja.</span></span></span></p>

            <p><span style="font-size:11.0pt"><span style="font-family:Montserrat">Daripada berfokus pada kefasihan sebagai tujuan, kami percaya pada pendekatan yang lebih sejalan dengan tujuan pelajar itu sendiri. Karena para pelajar sering kali memiliki tujuan tertentu ketika mempelajari bahasa baru, apa yang dianggap keberhasilan bagi satu pelajar mungkin terlihat sangat berbeda dengan garis finish pelajar lainnya. Itulah mengapa sangat sulit untuk mendefinisikan apa arti &ldquo;fasih&rdquo; dalam mempelajari bahasa baru.</span></span></p>
            ',
            ],
            [
                'question' => 'Bagaimana caranya kamu belajar dengan Arabic-Go?',
                'answere' => '<p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">Selamat datang di Arabic-Go! Kamu bisa belajar bahasa arab di Arabic-Go via aplikasi android, aplikasi IOS, dan web yang bisa diakses kapanpun dimanapun!</span></span></span></p>

            <p><span style="font-size:11.0pt"><span style="font-family:Montserrat">Di Arabic-Go, kamu dapat mempelajari bahasa arab dengan system learning path. Dengan system ini, masing-masing learning path akan memiliki beberapa kelas dengan tingkat kesulitan yang berbeda dimulai dari beginner hingga advanced. Setelah kamu menyelesaikan kelas dengan memenuhi semua kriteria kelulusan kelas tersebut, maka kamu berhak untuk mendapatkan sertifikat kelulusan kelas yang nantinya bisa kamu gunakan sebagai bukti bahwa kamu memiliki menguasai materi yang disediakan pada modul kelas tersebut.</span></span></p>
            ',
            ],
            [
                'question' => 'Apakah Arabic-Go akan selamanya gratis?',
                'answere' => '<p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">Untuk saat ini Arabic-Go memiliki beberapa kelas gratis, namun seiring waktu berjalan Arabic-Go juga akan meluncurkan kelas-kelas baru yang mungkin akan berbayar. Hal ini tentunya dengan berbagai pertimbangan yang berat. Kami akan memastikan biaya yang kamu keluarkan akan setimpal dengan ilmu yang akan kamu dapatkan di kelas tersebut!</span></span></span></p>
            ',
            ],
            [
                'question' => 'Apakah data profil saya aman?',
                'answere' => '<p><span style="font-size:11.0pt"><span style="font-family:Montserrat">Data adalah hal yang sangat berharga di era sekarang ini, dan kami memegang teguh prinsip tersebut. Arabic-Go berkomitmen untuk menjaga kepercayaan pengguna dengan melindungi semua informasi pengguna agar tetap aman.</span></span></p>
            ',
            ],
            [
                'question' => 'Bagaimana cara mendapatkan sertifikat?',
                'answere' => 'Coming Soon',
            ],
            [
                'question' => 'Apakah mentor Arabic-Go benar berpengalaman?',
                'answere' => '<p><span style="font-size:11.0pt"><span style="font-family:Montserrat">Semua modul yang kami luncurkan dikembangkan oleh para mentor yang sudah memiliki banyak pengalaman di bahasa arab atau sudah lama berkecimpung di dunia bahasa arab. Banyak dari mereka bahkan memang berprofesi sebagai guru, penulis buku, atau bahkan dosen bahasa arab. Untuk mengenal mereka lebih lanjut kamu bisa lihat profil mereka di halaman mentor.</span></span></p>
            ',
            ],
        ];

        $english = [
            [
                'question' => 'What is Arabic-go?',
                'answere' => '<p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">Arabic-Go is a certified Arabic learning online platform. Arabic-go started as a project to present automatic harokat for bald Arabic script as a result of a collaboration between Politeknik Caltex Riau, Indonesia and Advance Pact Sd. Bhd, Malaysia. Our mission is to provide easy and interesting Arabic learning for all people who may be interested. Arabic-Go also applies a learning path method, so you can learn Arabic based on your interests.</span></span></span></p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">For more details, visit our about page.</span></span></span></p>
                ',
            ],
            [
                'question' => 'Can you become fluent in Arabic with Arabic-Go?',
                'answere' => '<p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">You may aspire to be &ldquo;fluent&rdquo;, but &ldquo;comfortable&rdquo; is probably what you will get. You can be comfortable even as a beginner, depending on your goals in learning Arabic! Because the language you need to travel as a tourist for a week will be much different from the language you need to be a professional in the workplace.</span></span></span></p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">Rather than focusing on &ldquo;fluency&rdquo; as a goal, we believe in an approach that is more in line with the learner&#39;s own goals. Because learners often have specific goals when it comes to learning a new language, what is considered success for one learner may look very different from the finish line for another learner. That&#39;s why it&#39;s so hard to define what it means to be &ldquo;fluent&rdquo; in learning a new language.</span></span></span></p>
                ',
            ],
            [
                'question' => 'How will you learn with Arabic-Go?',
                'answere' => '<p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">Welcome to Arabic-Go! You can learn Arabic on Arabic-Go through the android application, IOS application, and the web which can be accessed anywhere anytime!</span></span></span></p>

                <p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">In Arabic-Go, you can learn Arabic with the learning path method. With this method, each learning path will have several classes with different levels of difficulty starting from beginners to advanced. After you complete the class by meeting all the graduation criteria for the class, then you are entitled to a class certificate which you can later use as proof that you have mastered the material provided in the class module.</span></span></span></p>
                ',
            ],
            [
                'question' => 'Will Arabic-Go cost no money forever?',
                'answere' => '<p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">For now Arabic-Go has some free classes, but over time Arabic-Go will also release some new classes that may be paid. This of course comes with many considerations. We will make sure that the costs you incur will be worth the knowledge you will gain in the class!</span></span></span></p>',
            ],
            [
                'question' => 'Is my profile secure?',
                'answere' => '<p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">Data is invaluable in today&#39;s world, and we stand by that principle. Arabic-Go is committed to maintaining user trust by protecting all user information to keep it safe.</span></span></span></p>
                ',
            ],
            [
                'question' => 'How to get a certificate?',
                'answere' => 'Coming Soon',
            ],
            [
                'question' => 'Are Arabic-Go mentors really experienced in Arabic?',
                'answere' => '<p><span style="font-size:11pt"><span style="font-family:Arial,sans-serif"><span style="font-family:Montserrat">All modules that we launch are made by mentors who already have a lot of experience in Arabic or have been in the Arabic field for a long time. Many of them even work as teachers, book writers, or even Arabic lecturers.</span></span></span></p>

                <p><span style="font-size:11.0pt"><span style="font-family:Montserrat">To get to know them more you can see their profile on the mentor page.</span></span></p>
                ',
            ],
        ];

        
        foreach($bahasa as $b) {
            $b['id_bahasa'] = 1;
            DB::table('faq')->insert($b);
        }

        
        foreach($english as $b) {
            $b['id_bahasa'] = 2;
            DB::table('faq')->insert($b);
        }

    }
}
