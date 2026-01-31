<?php

namespace App\Services;

use App\Models\Donor;
use App\Models\DonorDetail;
use App\Processor;

/**
 * Maps a Givebutter contact payload (array) to donors + donor_details table columns.
 * Aligns with donor/donor_details schema: donors (processor_id, processor, first_name, last_name, email, phone, address_line1/2, city, county, state, zip, country), donor_details (donor_id, birthday, occupation, organization_id, can_be_contacted).
 */
class GivebutterContactToDonorMapper
{
    public function mapToDonorAttributes(array $contact): array
    {
        $primaryAddress = $contact['primary_address'] ?? [];
        $id = $contact['id'] ?? null;

        return [
            'processor_id' => $id !== null ? (string) $id : null,
            'processor' => Processor::Givebutter->value,
            'first_name' => $contact['first_name'] ?? null,
            'last_name' => $contact['last_name'] ?? null,
            'email' => $this->normalizeEmail($contact['primary_email'] ?? null),
            'phone' => $contact['primary_phone'] ?? null,
            'address_line1' => $primaryAddress['address_1'] ?? null,
            'address_line2' => $primaryAddress['address_2'] ?? null,
            'city' => $primaryAddress['city'] ?? null,
            'county' => null,
            'state' => $primaryAddress['state'] ?? null,
            'zip' => $primaryAddress['zipcode'] ?? null,
            'country' => $primaryAddress['country'] ?? 'USA',
        ];
    }

    public function mapToDonorDetailAttributes(array $contact): array
    {
        $emailOptIn = (bool) ($contact['email_opt_in'] ?? false);
        $smsOptIn = (bool) ($contact['sms_opt_in'] ?? false);
        $canBeContacted = $emailOptIn || $smsOptIn;

        return [
            'birthday' => $this->parseDate($contact['dob'] ?? null),
            'occupation' => $contact['title'] ?? null,
            'organization_id' => null,
            'can_be_contacted' => $canBeContacted,
        ];
    }

    /**
     * Find or create donor and donor_detail from contact payload.
     * Skips if email is empty (donors.email is NOT NULL).
     * Uses processor_id (Givebutter contact id) for idempotent upsert.
     */
    public function upsertDonorFromContact(array $contact): ?Donor
    {
        $email = $this->normalizeEmail($contact['primary_email'] ?? null);
        if ($email === null || $email === '') {
            return null;
        }

        $processorId = isset($contact['id']) ? (string) $contact['id'] : null;
        if ($processorId === null) {
            return null;
        }

        $donorAttrs = $this->mapToDonorAttributes($contact);
        $donor = Donor::updateOrCreate(
            [
                'processor' => Processor::Givebutter->value,
                'processor_id' => $processorId,
            ],
            $donorAttrs
        );

        $detailAttrs = $this->mapToDonorDetailAttributes($contact);
        DonorDetail::updateOrCreate(
            ['donor_id' => $donor->id],
            $detailAttrs
        );

        return $donor;
    }

    private function normalizeEmail(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return trim($value);
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_string($value)) {
            $ts = strtotime($value);

            return $ts ? date('Y-m-d', $ts) : null;
        }

        return null;
    }
}
