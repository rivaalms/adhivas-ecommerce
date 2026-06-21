<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class UserAddressControllerDocs
{
    #[OA\Get(path: '/api/addresses', summary: 'Get User Addresses', security: [['bearerAuth' => []]], tags: ['addresses'])]
    #[OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Number of items per page', schema: new OA\Schema(type: 'integer', default: 15))]
    #[OA\Response(
        response: 200,
        description: 'Addresses retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Addresses retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'address_line', type: 'string', example: 'Jl. Merdeka No. 1'),
                            new OA\Property(property: 'subdistrict_id', type: 'string', example: '11'),
                            new OA\Property(property: 'subdistrict_name', type: 'string', example: 'Menteng'),
                            new OA\Property(property: 'district_id', type: 'string', example: '1'),
                            new OA\Property(property: 'district_name', type: 'string', example: 'Jakarta Pusat'),
                            new OA\Property(property: 'regency_id', type: 'string', example: '31'),
                            new OA\Property(property: 'regency_name', type: 'string', example: 'DKI Jakarta'),
                            new OA\Property(property: 'province_id', type: 'string', example: '31'),
                            new OA\Property(property: 'province_name', type: 'string', example: 'DKI Jakarta'),
                            new OA\Property(property: 'postal_code', type: 'string', example: '10310'),
                            new OA\Property(property: 'is_default', type: 'boolean', example: true),
                        ]
                    )),
                    new OA\Property(property: 'links', type: 'object', example: ['first' => '...', 'last' => '...']),
                    new OA\Property(property: 'meta', type: 'object', example: ['current_page' => 1, 'per_page' => 15, 'total' => 5]),
                ])
            ]
        )
    )]
    public function index() {}

    #[OA\Post(path: '/api/addresses', summary: 'Create Address', security: [['bearerAuth' => []]], tags: ['addresses'])]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'address_line', type: 'string', nullable: true, example: 'Jl. Merdeka No. 1'),
                new OA\Property(property: 'subdistrict_id', type: 'string', example: '11'),
                new OA\Property(property: 'subdistrict_name', type: 'string', example: 'Menteng'),
                new OA\Property(property: 'district_id', type: 'string', example: '1'),
                new OA\Property(property: 'district_name', type: 'string', example: 'Jakarta Pusat'),
                new OA\Property(property: 'regency_id', type: 'string', example: '31'),
                new OA\Property(property: 'regency_name', type: 'string', example: 'DKI Jakarta'),
                new OA\Property(property: 'province_id', type: 'string', example: '31'),
                new OA\Property(property: 'province_name', type: 'string', example: 'DKI Jakarta'),
                new OA\Property(property: 'postal_code', type: 'string', example: '10310'),
                new OA\Property(property: 'is_default', type: 'boolean', nullable: true, example: true),
            ],
            required: ['subdistrict_id', 'subdistrict_name', 'district_id', 'district_name', 'regency_id', 'regency_name', 'province_id', 'province_name', 'postal_code']
        )
    )])]
    #[OA\Response(
        response: 201,
        description: 'Address created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Address created successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'address_line', type: 'string', example: 'Jl. Merdeka No. 1'),
                ])
            ]
        )
    )]
    public function store() {}

    #[OA\Get(path: '/api/addresses/{id}', summary: 'Get Address Detail', security: [['bearerAuth' => []]], tags: ['addresses'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Address ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\Response(
        response: 200,
        description: 'Address retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Address retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'address_line', type: 'string', example: 'Jl. Merdeka No. 1'),
                    new OA\Property(property: 'is_default', type: 'boolean', example: true),
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Address not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Address not found')
            ]
        )
    )]
    public function show() {}

    #[OA\Put(path: '/api/addresses/{id}', summary: 'Update Address', security: [['bearerAuth' => []]], tags: ['addresses'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Address ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'address_line', type: 'string', nullable: true, example: 'Jl. Sudirman No. 2'),
                new OA\Property(property: 'subdistrict_id', type: 'string', example: '12'),
                new OA\Property(property: 'subdistrict_name', type: 'string', example: 'Senayan'),
                new OA\Property(property: 'district_id', type: 'string', example: '1'),
                new OA\Property(property: 'district_name', type: 'string', example: 'Jakarta Pusat'),
                new OA\Property(property: 'regency_id', type: 'string', example: '31'),
                new OA\Property(property: 'regency_name', type: 'string', example: 'DKI Jakarta'),
                new OA\Property(property: 'province_id', type: 'string', example: '31'),
                new OA\Property(property: 'province_name', type: 'string', example: 'DKI Jakarta'),
                new OA\Property(property: 'postal_code', type: 'string', example: '10270'),
                new OA\Property(property: 'is_default', type: 'boolean', example: true),
            ]
        )
    )])]
    #[OA\Response(
        response: 200,
        description: 'Address updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Address updated successfully'),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Address not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Address not found')
            ]
        )
    )]
    public function update() {}

    #[OA\Delete(path: '/api/addresses/{id}', summary: 'Delete Address', security: [['bearerAuth' => []]], tags: ['addresses'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Address ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\Response(
        response: 200,
        description: 'Address deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Address deleted successfully'),
                new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Address not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Address not found')
            ]
        )
    )]
    public function destroy() {}
}
