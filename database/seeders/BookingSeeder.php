<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingCancellationPolicy;
use App\Models\Resource;
use App\Models\ResourceAvailability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
  public function run(): void
  {
    DB::transaction(function () {

      $projectId = (int) env('CMS_PROJECT_ID', 1);
      $now       = now();

      // ─── Resource 1 — غرفة اجتماعات ──────────────────────────────────

      $room = Resource::create([
        'data_entry_id' => 1,
        'project_id'    => $projectId,
        'name'          => 'غرفة الاجتماعات A',
        'type'          => 'room',
        'capacity'      => 1,
        'status'        => Resource::STATUS_ACTIVE,
        'settings'      => ['floor' => 2, 'has_projector' => true],
      ]);

      // أوقات التوفر — الاثنين للجمعة 9 صباحاً - 5 مساءً
      foreach ([1, 2, 3, 4, 5] as $day) {
        ResourceAvailability::create([
          'resource_id'   => $room->id,
          'day_of_week'   => $day,
          'start_time'    => '09:00:00',
          'end_time'      => '17:00:00',
          'slot_duration' => 60, // ساعة لكل slot
          'is_active'     => true,
        ]);
      }

      // سياسة الإلغاء
      $this->createPolicies($room->id, [
        ['hours_before' => 24, 'refund_percentage' => 100, 'description' => 'إلغاء مجاني قبل 24 ساعة'],
        ['hours_before' => 12, 'refund_percentage' => 50,  'description' => 'استرداد 50% قبل 12 ساعة'],
        ['hours_before' => 0,  'refund_percentage' => 0,   'description' => 'لا استرداد أقل من 12 ساعة'],
      ]);

      // ─── Resource 2 — ملعب رياضي ──────────────────────────────────────

      $court = Resource::create([
        'data_entry_id' => 2,
        'project_id'    => $projectId,
        'name'          => 'الملعب الرياضي',
        'type'          => 'court',
        'capacity'      => 1,
        'status'        => Resource::STATUS_ACTIVE,
        'settings'      => ['surface' => 'concrete', 'lighting' => true],
      ]);

      // أوقات التوفر — كل أيام الأسبوع
      foreach (range(0, 6) as $day) {
        ResourceAvailability::create([
          'resource_id'   => $court->id,
          'day_of_week'   => $day,
          'start_time'    => '07:00:00',
          'end_time'      => '22:00:00',
          'slot_duration' => 90, // ساعة ونص لكل slot
          'is_active'     => true,
        ]);
      }

      // سياسة الإلغاء
      $this->createPolicies($court->id, [
        ['hours_before' => 48, 'refund_percentage' => 100, 'description' => 'إلغاء مجاني قبل 48 ساعة'],
        ['hours_before' => 24, 'refund_percentage' => 50,  'description' => 'استرداد 50% قبل 24 ساعة'],
        ['hours_before' => 0,  'refund_percentage' => 0,   'description' => 'لا استرداد أقل من 24 ساعة'],
      ]);

      // ─── Resource 3 — موعد طبيب (الجمعة إجازة) ───────────────────────

      $doctor = Resource::create([
        'data_entry_id' => 3,
        'project_id'    => $projectId,
        'name'          => 'د. أحمد — طب عام',
        'type'          => 'doctor',
        'capacity'      => 1,
        'status'        => Resource::STATUS_ACTIVE,
        'settings'      => ['specialty' => 'general', 'consultation_fee' => 50],
      ]);

      // الاثنين - الخميس 9 صباحاً - 3 مساءً
      foreach ([1, 2, 3, 4] as $day) {
        ResourceAvailability::create([
          'resource_id'   => $doctor->id,
          'day_of_week'   => $day,
          'start_time'    => '09:00:00',
          'end_time'      => '15:00:00',
          'slot_duration' => 30, // نص ساعة لكل موعد
          'is_active'     => true,
        ]);
      }

      // الجمعة — متاحة لكن is_active = false (إجازة)
      ResourceAvailability::create([
        'resource_id'   => $doctor->id,
        'day_of_week'   => 5,
        'start_time'    => '09:00:00',
        'end_time'      => '12:00:00',
        'slot_duration' => 30,
        'is_active'     => false,
      ]);

      // سياسة الإلغاء
      $this->createPolicies($doctor->id, [
        ['hours_before' => 24, 'refund_percentage' => 100, 'description' => 'إلغاء مجاني قبل 24 ساعة'],
        ['hours_before' => 2,  'refund_percentage' => 0,   'description' => 'لا استرداد أقل من ساعتين'],
      ]);

      // ─── Bookings — حجوزات تجريبية ────────────────────────────────────

      // حجز مؤكد على غرفة الاجتماعات — غداً
      Booking::create([
        'resource_id'  => $room->id,
        'user_id'      => 1,
        'project_id'   => $projectId,
        'payment_id'   => null,
        'start_at'     => $now->copy()->addDay()->setTime(10, 0),
        'end_at'       => $now->copy()->addDay()->setTime(11, 0),
        'status'       => Booking::STATUS_CONFIRMED,
        'amount'       => 100.00,
        'currency'     => 'USD',
        'notes'        => 'اجتماع فريق التطوير',
      ]);

      // حجز pending على الملعب — بعد يومين
      Booking::create([
        'resource_id'  => $court->id,
        'user_id'      => 1,
        'project_id'   => $projectId,
        'payment_id'   => null,
        'start_at'     => $now->copy()->addDays(2)->setTime(18, 0),
        'end_at'       => $now->copy()->addDays(2)->setTime(19, 30),
        'status'       => Booking::STATUS_PENDING,
        'amount'       => 75.00,
        'currency'     => 'USD',
        'notes'        => null,
      ]);

      // حجز ملغى — بالأمس
      Booking::create([
        'resource_id'        => $doctor->id,
        'user_id'            => 1,
        'project_id'         => $projectId,
        'payment_id'         => null,
        'start_at'           => $now->copy()->subDay()->setTime(9, 0),
        'end_at'             => $now->copy()->subDay()->setTime(9, 30),
        'status'             => Booking::STATUS_CANCELLED,
        'amount'             => 50.00,
        'currency'           => 'USD',
        'cancellation_reason' => 'ظروف طارئة',
        'refund_amount'      => 50.00,
      ]);

      // حجز مكتمل — قبل أسبوع
      Booking::create([
        'resource_id'  => $room->id,
        'user_id'      => 1,
        'project_id'   => $projectId,
        'payment_id'   => null,
        'start_at'     => $now->copy()->subWeek()->setTime(14, 0),
        'end_at'       => $now->copy()->subWeek()->setTime(15, 0),
        'status'       => Booking::STATUS_COMPLETED,
        'amount'       => 100.00,
        'currency'     => 'USD',
        'notes'        => 'مراجعة المشروع',
      ]);

      // حجز no_show — قبل 3 أيام
      Booking::create([
        'resource_id'  => $doctor->id,
        'user_id'      => 1,
        'project_id'   => $projectId,
        'payment_id'   => null,
        'start_at'     => $now->copy()->subDays(3)->setTime(11, 0),
        'end_at'       => $now->copy()->subDays(3)->setTime(11, 30),
        'status'       => Booking::STATUS_NO_SHOW,
        'amount'       => 50.00,
        'currency'     => 'USD',
        'notes'        => null,
      ]);
    });
  }

  // ─── Helper ───────────────────────────────────────────────────────────────

  private function createPolicies(int $resourceId, array $policies): void
  {
    foreach ($policies as $policy) {
      BookingCancellationPolicy::create([
        'resource_id'       => $resourceId,
        'hours_before'      => $policy['hours_before'],
        'refund_percentage' => $policy['refund_percentage'],
        'description'       => $policy['description'],
      ]);
    }
  }
}
