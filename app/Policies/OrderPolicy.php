<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

/**
 * Authorizes actions on Order models.
 *
 * Download receipt rules:
 *  - Administrators ('A')   → always allowed
 *  - Customers ('C')        → only their own orders AND order is 'closed'
 *  - Employees ('F') / anyone else → forbidden (403)
 */
class OrderPolicy
{
    /**
     * Allow admins to bypass all policy checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->user_type === 'A') {
            return true;
        }

        return null;
    }

    /**
     * Determines if the user may download/view the PDF receipt of an order.
     */
    public function downloadReceipt(User $user, Order $order): bool
    {
        // Only customers who own the order and when it is closed
        return $user->user_type === 'C'
            && $order->status === 'closed'
            && $order->customer_id === $user->id;
    }

    /**
     * Determines if the user may view the order details.
     */
    public function view(User $user, Order $order): bool
    {
        // Admins pass via before(). Customers only see their own.
        return $user->user_type === 'C'
            && $order->customer_id === $user->id;
    }
}
