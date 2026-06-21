<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class ProductControllerDocs
{
    #[OA\Get(path: '/api/products', summary: 'Get All Products', security: [['bearerAuth' => []]], tags: ['products'])]
    #[OA\Parameter(name: 'category_id', in: 'query', required: false, description: 'Filter by category ID', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'search', in: 'query', required: false, description: 'Search query for product name', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'sort_by', in: 'query', required: false, description: 'Sort by field (price, created_at)', schema: new OA\Schema(type: 'string', enum: ['price', 'created_at']))]
    #[OA\Parameter(name: 'sort_dir', in: 'query', required: false, description: 'Sort direction (asc/desc)', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'asc'))]
    #[OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Number of items per page', schema: new OA\Schema(type: 'integer', default: 15))]
    #[OA\Response(
        response: 200,
        description: 'Products retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Products retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Smartphone'),
                            new OA\Property(property: 'description', type: 'string', example: 'High-end smartphone'),
                            new OA\Property(property: 'price', type: 'number', format: 'float', example: 999.99),
                            new OA\Property(property: 'stock_quantity', type: 'integer', example: 50),
                            new OA\Property(property: 'image_url', type: 'string', example: 'http://localhost:8000/storage/products/image.jpg'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        ]
                    )),
                    new OA\Property(property: 'links', type: 'object', example: ['first' => '...', 'last' => '...']),
                    new OA\Property(property: 'meta', type: 'object', example: ['current_page' => 1, 'per_page' => 15, 'total' => 100]),
                ])
            ]
        )
    )]
    public function index() {}

    #[OA\Post(path: '/api/products', summary: 'Create Product', security: [['bearerAuth' => []]], tags: ['products'])]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Smartphone'),
                new OA\Property(property: 'description', type: 'string', example: 'High-end smartphone'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 999.99),
                new OA\Property(property: 'stock_quantity', type: 'integer', example: 50),
                new OA\Property(property: 'categories', type: 'array', items: new OA\Items(type: 'integer'), example: [1, 2]),
            ],
            required: ['name', 'description', 'price', 'stock_quantity', 'categories']
        )
    )])]
    #[OA\Response(
        response: 201,
        description: 'Product created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Product created successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'Smartphone'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 999.99),
                ])
            ]
        )
    )]
    public function store() {}

    #[OA\Get(path: '/api/products/{id}', summary: 'Get Product Detail', security: [['bearerAuth' => []]], tags: ['products'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Product ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\Response(
        response: 200,
        description: 'Product retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Product retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'Smartphone'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 999.99),
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Product not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Product not found')
            ]
        )
    )]
    public function show() {}

    #[OA\Put(path: '/api/products/{id}', summary: 'Update Product', security: [['bearerAuth' => []]], tags: ['products'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Product ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'New Smartphone'),
                new OA\Property(property: 'description', type: 'string', example: 'Updated description'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 899.99),
                new OA\Property(property: 'stock_quantity', type: 'integer', example: 100),
                new OA\Property(property: 'categories', type: 'array', items: new OA\Items(type: 'integer'), example: [1]),
            ]
        )
    )])]
    #[OA\Response(
        response: 200,
        description: 'Product updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Product updated successfully'),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Product not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Product not found')
            ]
        )
    )]
    public function update() {}

    #[OA\Delete(path: '/api/products/{id}', summary: 'Delete Product', security: [['bearerAuth' => []]], tags: ['products'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Product ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\Response(
        response: 200,
        description: 'Product deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Product deleted successfully'),
                new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Product not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Product not found')
            ]
        )
    )]
    public function destroy() {}

    #[OA\Post(path: '/api/products/{id}/upload', summary: 'Upload Product Image', security: [['bearerAuth' => []]], tags: ['products'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Product ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'multipart/form-data',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Product image file (jpeg, png, jpg, gif, svg, webp)'),
            ],
            required: ['image']
        )
    )])]
    #[OA\Response(
        response: 200,
        description: 'Image uploaded successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Image uploaded successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'image_url', type: 'string', example: 'http://localhost:8000/storage/products/image.jpg'),
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Product not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Product not found')
            ]
        )
    )]
    public function uploadImage() {}
}
