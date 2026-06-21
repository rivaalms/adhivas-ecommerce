<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class DashboardControllerDocs
{
    #[OA\Get(path: '/api/dashboard/stats', summary: 'Get Dashboard Stats', security: [['bearerAuth' => []]], tags: ['dashboard'])]
    #[OA\Parameter(name: 'start_date', in: 'query', required: false, description: 'Start Date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Parameter(name: 'end_date', in: 'query', required: false, description: 'End Date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Response(
        response: 200,
        description: 'Dashboard stats retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Dashboard stats retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'total_revenue', type: 'number', format: 'float', example: 150000.50),
                    new OA\Property(property: 'total_orders', type: 'integer', example: 150),
                    new OA\Property(property: 'avg_order_value', type: 'number', format: 'float', example: 1000.00),
                    new OA\Property(property: 'meta', type: 'object', properties: [
                        new OA\Property(property: 'start_date', type: 'string', format: 'date', nullable: true),
                        new OA\Property(property: 'end_date', type: 'string', format: 'date', nullable: true),
                    ])
                ])
            ]
        )
    )]
    public function stats() {}

    #[OA\Get(path: '/api/dashboard/status-breakdown', summary: 'Get Order Status Breakdown', security: [['bearerAuth' => []]], tags: ['dashboard'])]
    #[OA\Parameter(name: 'start_date', in: 'query', required: false, description: 'Start Date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Parameter(name: 'end_date', in: 'query', required: false, description: 'End Date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Response(
        response: 200,
        description: 'Order status breakdown retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Order status breakdown retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'order_status_breakdown', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'status', type: 'string', example: 'completed'),
                            new OA\Property(property: 'count', type: 'integer', example: 45),
                            new OA\Property(property: 'total_revenue', type: 'number', format: 'float', example: 45000.00),
                        ]
                    )),
                    new OA\Property(property: 'meta', type: 'object', properties: [
                        new OA\Property(property: 'start_date', type: 'string', format: 'date', nullable: true),
                        new OA\Property(property: 'end_date', type: 'string', format: 'date', nullable: true),
                    ])
                ])
            ]
        )
    )]
    public function statusBreakdown() {}

    #[OA\Get(path: '/api/dashboard/sales-trend', summary: 'Get Sales Trend', security: [['bearerAuth' => []]], tags: ['dashboard'])]
    #[OA\Parameter(name: 'start_date', in: 'query', required: false, description: 'Start Date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Parameter(name: 'end_date', in: 'query', required: false, description: 'End Date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Response(
        response: 200,
        description: 'Sales trend retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Sales trend retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'sales_trend', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'date', type: 'string', format: 'date', example: '2023-10-01'),
                            new OA\Property(property: 'revenue', type: 'number', format: 'float', example: 1500.00),
                            new OA\Property(property: 'order_count', type: 'integer', example: 5),
                        ]
                    )),
                    new OA\Property(property: 'meta', type: 'object', properties: [
                        new OA\Property(property: 'start_date', type: 'string', format: 'date'),
                        new OA\Property(property: 'end_date', type: 'string', format: 'date'),
                    ])
                ])
            ]
        )
    )]
    public function salesTrend() {}
}
