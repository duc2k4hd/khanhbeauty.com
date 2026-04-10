<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Lấy danh sách nhân viên phục vụ
     */
    public function getStaffs(Request $request)
    {
        // Lấy các user có role admin hoặc staff trực tiếp từ cột role
        $staffs = User::whereIn('role', ['admin', 'staff'])
            ->where('is_active', true)
            ->get(['id', 'full_name', 'email']);

        return response()->json([
            'success' => true,
            'data' => $staffs
        ]);
    }

    /**
     * Trả về danh sách khung giờ còn trống của một nhân viên trong ngày chỉ định
     */
    public function getSlots(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        // Logic khung giờ đơn giản từ 08:00 đến 18:00, mỗi slot 1 tiếng
        $availableSlots = [];
        $startHour = 8;
        $endHour = 18;
        
        $date = $request->date;
        $staffId = $request->staff_id;

        // Lấy các khung giờ đã book trong ngày
        $bookedTimes = Booking::where('staff_id', $staffId)
            ->whereDate('booking_date', $date)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->pluck('booking_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })->toArray();

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $timeString = sprintf('%02d:00', $hour);
            
            // Xóa slot đã quá hạn nếu là ngày hôm nay
            if (Carbon::parse($date)->isToday()) {
                if ($hour <= now()->hour) {
                    continue;
                }
            }
            
            if (!in_array($timeString, $bookedTimes)) {
                $availableSlots[] = $timeString;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $availableSlots
        ]);
    }

    /**
     * Submit form đặt lịch
     */
    public function submitBooking(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'variant_id' => 'required|exists:service_variants,id',
            'staff_id' => 'required|exists:users,id',
            'booking_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'guest_name' => 'required|string|max:191',
            'guest_phone' => 'required|string|max:20',
            'guest_email' => 'nullable|email|max:191',
            'notes' => 'nullable|string'
        ]);

        $variant = ServiceVariant::findOrFail($validated['variant_id']);
        
        // Thêm các thông tin mặc định
        $bookingData = array_merge($validated, [
            'booking_code' => 'KB' . strtoupper(Str::random(6)),
            'total_amount' => $variant->price,
            'status' => 'pending',
            'source' => 'website',
        ]);

        // Tính end_time (giả sử mỗi dịch vụ kéo dài theo duration hoặc mặc định 60p)
        $service = Service::find($validated['service_id']);
        $duration = (int) ($service ? ($service->duration_minutes ?: 60) : 60);
        
        $bookingTime = Carbon::createFromFormat('Y-m-d H:i', $validated['booking_date'] . ' ' . $validated['booking_time']);
        $bookingData['end_time'] = $bookingTime->copy()->addMinutes($duration)->format('H:i:s');

        // Tạo Booking
        $booking = Booking::create($bookingData);

        return response()->json([
            'success' => true,
            'booking_code' => $booking->booking_code,
            'message' => 'Đặt lịch thành công! Mã đặt lịch của bạn là: ' . $booking->booking_code
        ]);
    }
}
