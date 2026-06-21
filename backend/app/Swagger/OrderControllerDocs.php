<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class OrderControllerDocs
{
    #[OA\Get(path: '/api/orders', summary: 'Get All Orders', security: [['bearerAuth' => []]], tags: ['orders'])]
    #[OA\Parameter(name: 'status', in: 'query', required: false, description: 'Filter by order status', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'start_date', in: 'query', required: false, description: 'Start Date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Parameter(name: 'end_date', in: 'query', required: false, description: 'End Date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Parameter(name: 'user_id', in: 'query', required: false, description: 'Filter by user ID (admin only)', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'sort_dir', in: 'query', required: false, description: 'Sort direction (asc/desc)', schema: new OA\Schema(type: 'string', default: 'desc'))]
    #[OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Number of items per page', schema: new OA\Schema(type: 'integer', default: 15))]
    #[OA\Response(
        response: 200,
        description: 'Orders retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Orders retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'user_id', type: 'integer', example: 1),
                            new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 500.00),
                            new OA\Property(property: 'status', type: 'string', example: 'pending'),
                            new OA\Property(property: 'order_date', type: 'string', format: 'date-time'),
                        ]
                    )),
                    new OA\Property(property: 'links', type: 'object', example: ['first' => '...', 'last' => '...', 'prev' => null, 'next' => null]),
                    new OA\Property(property: 'meta', type: 'object', example: ['current_page' => 1, 'from' => 1, 'last_page' => 1, 'path' => '...', 'per_page' => 15, 'to' => 15, 'total' => 50]),
                ])
            ]
        )
    )]
    public function index() {}

    #[OA\Post(path: '/api/orders', summary: 'Create Order (Checkout)', security: [['bearerAuth' => []]], tags: ['orders'])]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'user_address_id', type: 'integer', example: 1),
                new OA\Property(property: 'items', type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'product_id', type: 'integer', example: 1),
                        new OA\Property(property: 'quantity', type: 'integer', example: 2),
                    ]
                ))
            ],
            required: ['user_address_id', 'items']
        )
    )])]
    #[OA\Response(
        response: 201,
        description: 'Order created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Order created successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 500.00),
                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
                ])
            ]
        )
    )]
    public function store() {}

    #[OA\Get(path: '/api/orders/{id}', summary: 'Get Order Detail', security: [['bearerAuth' => []]], tags: ['orders'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Order ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\Response(
        response: 200,
        description: 'Order retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Order retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 500.00),
                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Order not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Order not found')
            ]
        )
    )]
    public function show() {}

    #[OA\Put(path: '/api/orders/{id}', summary: 'Update Order Address', security: [['bearerAuth' => []]], tags: ['orders'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Order ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'user_address_id', type: 'integer', example: 2),
            ],
            required: ['user_address_id']
        )
    )])]
    #[OA\Response(
        response: 200,
        description: 'Order updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Order updated successfully'),
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Only pending orders can be updated',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Only pending orders can be updated')
            ]
        )
    )]
    public function update() {}

    #[OA\Patch(path: '/api/orders/{id}/status', summary: 'Update Order Status', security: [['bearerAuth' => []]], tags: ['orders'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Order ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'cancelled'),
            ],
            required: ['status']
        )
    )])]
    #[OA\Response(
        response: 200,
        description: 'Order status updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Order status updated successfully'),
            ]
        )
    )]
    #[OA\Response(
        response: 403,
        description: 'Forbidden',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Forbidden')
            ]
        )
    )]
    public function updateStatus() {}

    #[OA\Delete(path: '/api/orders/{id}', summary: 'Delete Order (Admin)', security: [['bearerAuth' => []]], tags: ['orders'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Order ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\Response(
        response: 200,
        description: 'Order deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Order deleted successfully'),
                new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Order not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Order not found')
            ]
        )
    )]
    public function destroy() {}
}
