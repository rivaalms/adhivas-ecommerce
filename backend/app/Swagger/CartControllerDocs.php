<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class CartControllerDocs
{
    #[OA\Get(path: '/api/cart', summary: 'Get Cart', security: [['bearerAuth' => []]], tags: ['cart'])]
    #[OA\Response(
        response: 200,
        description: 'Cart retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Cart retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                    new OA\Property(property: 'items', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'cart_id', type: 'integer', example: 1),
                            new OA\Property(property: 'product_id', type: 'integer', example: 1),
                            new OA\Property(property: 'quantity', type: 'integer', example: 2),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                        ]
                    ))
                ])
            ]
        )
    )]
    public function index() {}

    #[OA\Post(path: '/api/cart', summary: 'Add or Update Cart Item', security: [['bearerAuth' => []]], tags: ['cart'])]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                new OA\Property(property: 'quantity', type: 'integer', example: 2),
            ],
            required: ['product_id', 'quantity']
        )
    )])]
    #[OA\Response(
        response: 200,
        description: 'Cart updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Cart updated successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                    new OA\Property(property: 'items', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'cart_id', type: 'integer', example: 1),
                            new OA\Property(property: 'product_id', type: 'integer', example: 1),
                            new OA\Property(property: 'quantity', type: 'integer', example: 2),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                        ]
                    ))
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Insufficient stock available',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Insufficient stock available')
            ]
        )
    )]
    public function store() {}

    #[OA\Delete(path: '/api/cart/items/{id}', summary: 'Remove Cart Item', security: [['bearerAuth' => []]], tags: ['cart'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Cart Item ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\Response(
        response: 200,
        description: 'Cart item removed successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Cart item removed successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                    new OA\Property(property: 'items', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'cart_id', type: 'integer', example: 1),
                            new OA\Property(property: 'product_id', type: 'integer', example: 1),
                            new OA\Property(property: 'quantity', type: 'integer', example: 2),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                        ]
                    ))
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Cart item not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Cart item not found'),
                new OA\Property(property: 'data', type: 'null', example: null)
            ]
        )
    )]
    public function destroy() {}
}
