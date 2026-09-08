<?php

class ProfitModel extends Model
{
    public function summary()
    {
        $sql = "
            SELECT
                SUM(si.total) as revenue,
                SUM(si.cost_price * si.quantity) as cost,
                SUM(si.total - (si.cost_price * si.quantity)) as profit
            FROM sale_items si
            JOIN sales s ON s.id = si.sale_id
        ";

        return $this->db->query($sql)->single();
    }

    public function today()
    {
        $sql = "
            SELECT
                SUM(si.total) as revenue,
                SUM(si.cost_price * si.quantity) as cost,
                SUM(si.total - (si.cost_price * si.quantity)) as profit
            FROM sale_items si
            JOIN sales s ON s.id = si.sale_id
            WHERE DATE(s.created_at) = CURDATE()
        ";

        return $this->db->query($sql)->single();
    }

    public function dailyReport()
    {
        $sql = "
            SELECT 
                DATE(s.created_at) as date,
                SUM(si.total) as revenue,
                SUM(si.cost_price * si.quantity) as cost,
                SUM(si.total - (si.cost_price * si.quantity)) as profit
            FROM sales s
            JOIN sale_items si ON s.id = si.sale_id
            GROUP BY DATE(s.created_at)
            ORDER BY date DESC
        ";

        return $this->db->query($sql)->resultSet();
    }

    public function topProducts()
{
    $sql = "
        SELECT 
            si.product_id,
            p.name,
            SUM(si.total - (si.cost_price * si.quantity)) as profit,
            SUM(si.quantity) as qty_sold
        FROM sale_items si
        JOIN inventory p ON p.id = si.product_id
        GROUP BY si.product_id, p.name
        ORDER BY profit DESC
        LIMIT 5
    ";

    return $this->db->query($sql)->resultSet();
}

public function lossProducts()
{
    $sql = "
        SELECT 
            si.product_id,
            p.name,
            SUM(si.quantity) as qty_sold,
            SUM(si.total) as revenue,
            SUM(si.cost_price * si.quantity) as cost,
            SUM(si.total - (si.cost_price * si.quantity)) as profit
        FROM sale_items si
        JOIN inventory p ON p.id = si.product_id
        GROUP BY si.product_id, p.name
        HAVING profit < 0
        ORDER BY profit ASC
        LIMIT 10
    ";

    return $this->db->query($sql)->resultSet();
}
}