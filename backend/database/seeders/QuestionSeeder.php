<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createQuestionsForFundamentals();
        $this->createQuestionsForAnatomy();
        $this->createQuestionsForPharmacology();
        $this->createQuestionsForClinicalMedicine();
    }

    private function createQuestionsForFundamentals()
    {
        $subject = Subject::where('name', 'Ayurveda Fundamentals')->first();
        $topics = $subject->topics;

        $questions = [
            [
                'topic' => 'Tridosha Theory',
                'difficulty' => 'basic',
                'question' => 'Which of the following is NOT one of the three doshas in Ayurveda?',
                'options' => ['Vata', 'Pitta', 'Kapha', 'Agni'],
                'correct' => 'D',
                'explanation' => 'The three doshas are Vata, Pitta, and Kapha. Agni is the digestive fire, not a dosha.'
            ],
            [
                'topic' => 'Panchamahabhuta',
                'difficulty' => 'basic',
                'question' => 'How many basic elements (Panchamahabhuta) are there in Ayurveda?',
                'options' => ['Three', 'Four', 'Five', 'Six'],
                'correct' => 'C',
                'explanation' => 'Panchamahabhuta means five (pancha) great (maha) elements (bhuta): earth, water, fire, air, and space.'
            ],
            [
                'topic' => 'Tridosha Theory',
                'difficulty' => 'intermediate',
                'question' => 'Which dosha is primarily responsible for movement and nervous system functions?',
                'options' => ['Vata', 'Pitta', 'Kapha', 'All three equally'],
                'correct' => 'A',
                'explanation' => 'Vata dosha governs all movement in the body, including nervous system functions, circulation, and elimination.'
            ],
            [
                'topic' => 'Prakriti and Vikriti',
                'difficulty' => 'intermediate',
                'question' => 'What is Prakriti in Ayurveda?',
                'options' => ['Current health state', 'Disease condition', 'Natural constitution', 'Treatment method'],
                'correct' => 'C',
                'explanation' => 'Prakriti is the natural constitution determined at conception, while Vikriti is the current state of health.'
            ],
            [
                'topic' => 'Ashtanga Ayurveda',
                'difficulty' => 'advanced',
                'question' => 'Which branch of Ashtanga Ayurveda deals with mental health and psychiatric disorders?',
                'options' => ['Kaumara Bhritya', 'Shalya Tantra', 'Bhuta Vidya', 'Rasayana Tantra'],
                'correct' => 'C',
                'explanation' => 'Bhuta Vidya is the branch of Ayurveda that deals with psychiatry, psychology, and mental health disorders.'
            ]
        ];

        foreach ($questions as $q) {
            $topic = $topics->where('name', $q['topic'])->first();
            if ($topic) {
                Question::create([
                    'subject_id' => $subject->id,
                    'topic_id' => $topic->id,
                    'question_text' => $q['question'],
                    'option_a' => $q['options'][0],
                    'option_b' => $q['options'][1],
                    'option_c' => $q['options'][2],
                    'option_d' => $q['options'][3],
                    'correct_answer' => $q['correct'],
                    'explanation' => $q['explanation'],
                    'difficulty_level' => $q['difficulty'],
                    'is_active' => true,
                ]);
            }
        }
    }

    private function createQuestionsForAnatomy()
    {
        $subject = Subject::where('name', 'Anatomy & Physiology')->first();
        $topics = $subject->topics;

        $questions = [
            [
                'topic' => 'Marma Points',
                'difficulty' => 'basic',
                'question' => 'How many Marma points are described in classical Ayurvedic texts?',
                'options' => ['107', '108', '109', '110'],
                'correct' => 'A',
                'explanation' => 'Classical Ayurvedic texts describe 107 Marma points - vital anatomical locations where life energy is concentrated.'
            ],
            [
                'topic' => 'Srotas System',
                'difficulty' => 'intermediate',
                'question' => 'Which Srotas is responsible for carrying nutrients from digested food?',
                'options' => ['Pranavaha Srotas', 'Rasavaha Srotas', 'Raktavaha Srotas', 'Majjavaha Srotas'],
                'correct' => 'B',
                'explanation' => 'Rasavaha Srotas carries the nutrient plasma (rasa) derived from digested food throughout the body.'
            ],
            [
                'topic' => 'Ojas, Tejas, Prana',
                'difficulty' => 'advanced',
                'question' => 'Which of the following is considered the essence of Kapha dosha?',
                'options' => ['Ojas', 'Tejas', 'Prana', 'Agni'],
                'correct' => 'A',
                'explanation' => 'Ojas is the subtle essence of Kapha dosha, representing immunity, strength, and vitality.'
            ]
        ];

        foreach ($questions as $q) {
            $topic = $topics->where('name', $q['topic'])->first();
            if ($topic) {
                Question::create([
                    'subject_id' => $subject->id,
                    'topic_id' => $topic->id,
                    'question_text' => $q['question'],
                    'option_a' => $q['options'][0],
                    'option_b' => $q['options'][1],
                    'option_c' => $q['options'][2],
                    'option_d' => $q['options'][3],
                    'correct_answer' => $q['correct'],
                    'explanation' => $q['explanation'],
                    'difficulty_level' => $q['difficulty'],
                    'is_active' => true,
                ]);
            }
        }
    }

    private function createQuestionsForPharmacology()
    {
        $subject = Subject::where('name', 'Pharmacology')->first();
        $topics = $subject->topics;

        $questions = [
            [
                'topic' => 'Rasa Panchak',
                'difficulty' => 'basic',
                'question' => 'How many tastes (Rasa) are recognized in Ayurveda?',
                'options' => ['Four', 'Five', 'Six', 'Seven'],
                'correct' => 'C',
                'explanation' => 'Ayurveda recognizes six tastes: sweet, sour, salty, pungent, bitter, and astringent.'
            ],
            [
                'topic' => 'Dravyaguna',
                'difficulty' => 'intermediate',
                'question' => 'Which property (Guna) is opposite to Guru (heavy)?',
                'options' => ['Ruksha', 'Laghu', 'Sheeta', 'Ushna'],
                'correct' => 'B',
                'explanation' => 'Laghu (light) is the opposite property to Guru (heavy) in Ayurvedic pharmacology.'
            ],
            [
                'topic' => 'Classical Formulations',
                'difficulty' => 'advanced',
                'question' => 'Triphala is a combination of which three fruits?',
                'options' => ['Amalaki, Bibhitaki, Haritaki', 'Amalaki, Draksha, Kharjura', 'Bibhitaki, Pippali, Maricha', 'Haritaki, Vidanga, Chavya'],
                'correct' => 'A',
                'explanation' => 'Triphala consists of Amalaki (Emblica officinalis), Bibhitaki (Terminalia bellirica), and Haritaki (Terminalia chebula).'
            ]
        ];

        foreach ($questions as $q) {
            $topic = $topics->where('name', $q['topic'])->first();
            if ($topic) {
                Question::create([
                    'subject_id' => $subject->id,
                    'topic_id' => $topic->id,
                    'question_text' => $q['question'],
                    'option_a' => $q['options'][0],
                    'option_b' => $q['options'][1],
                    'option_c' => $q['options'][2],
                    'option_d' => $q['options'][3],
                    'correct_answer' => $q['correct'],
                    'explanation' => $q['explanation'],
                    'difficulty_level' => $q['difficulty'],
                    'is_active' => true,
                ]);
            }
        }
    }

    private function createQuestionsForClinicalMedicine()
    {
        $subject = Subject::where('name', 'Clinical Medicine')->first();
        $topics = $subject->topics;

        $questions = [
            [
                'topic' => 'Nidana Panchak',
                'difficulty' => 'intermediate',
                'question' => 'Which is NOT one of the five components of Nidana Panchak?',
                'options' => ['Nidana', 'Purvarupa', 'Rupa', 'Chikitsa'],
                'correct' => 'D',
                'explanation' => 'Nidana Panchak includes: Nidana (causative factors), Purvarupa (prodromal symptoms), Rupa (clinical features), Upashaya (therapeutic test), and Samprapti (pathogenesis). Chikitsa (treatment) is not part of it.'
            ],
            [
                'topic' => 'Panchakarma',
                'difficulty' => 'basic',
                'question' => 'How many main procedures are included in Panchakarma?',
                'options' => ['Three', 'Four', 'Five', 'Six'],
                'correct' => 'C',
                'explanation' => 'Panchakarma includes five main purification procedures: Vamana, Virechana, Niruha Basti, Anuvasana Basti, and Nasya.'
            ]
        ];

        foreach ($questions as $q) {
            $topic = $topics->where('name', $q['topic'])->first();
            if ($topic) {
                Question::create([
                    'subject_id' => $subject->id,
                    'topic_id' => $topic->id,
                    'question_text' => $q['question'],
                    'option_a' => $q['options'][0],
                    'option_b' => $q['options'][1],
                    'option_c' => $q['options'][2],
                    'option_d' => $q['options'][3],
                    'correct_answer' => $q['correct'],
                    'explanation' => $q['explanation'],
                    'difficulty_level' => $q['difficulty'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
