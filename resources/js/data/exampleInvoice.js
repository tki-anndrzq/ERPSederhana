// Hanya untuk menu Contoh Invoice dan Contoh Pembayaran.
export default {
    invoice_number: 'INV-CONTOH-0001',
    public_token: 'demo',
    amount: '120000.00',
    status: 'unpaid',
    created_at: '2026-10-21 10:00',
    paid_at: null,
    payment: null,
    booking: {
        booking_number: 'BK-CONTOH-0001',
        destination: 'Jepang',
        start_date: '2026-10-22',
        end_date: '2026-10-24',
        duration_days: 3,
        daily_rate: '40000.00',
        customer: { name: 'Pelanggan Contoh', phone: '08xxxxxxxxxx', email: 'pelanggan@example.com' },
        rental_package: { name: 'Jepang Harian' },
        device: { code: 'WIFI-001' },
    },
};
