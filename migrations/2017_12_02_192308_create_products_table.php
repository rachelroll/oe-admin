<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->default('')->comment('产品名称/型号');
            $table->tinyInteger('cat_id')->default(0)->comment('分类ID');
            $table->tinyInteger('type')->default(0)->comment('产品级别 0:普通产品 1:主打产品 2:特色产品');
            $table->tinyInteger('enabled')->default(0)->comment('启用禁用 0:禁用 1:启用');
            $table->string('cover')->default('')->comment('产品封面图');
            $table->string('imgs')->default('')->comment('产品细节图');
            $table->string('intro_title')->default('')->comment('简介Title');
            $table->string('intro')->default('')->comment('产品简介');
            $table->string('attr')->default('')->comment('产品属性');
            $table->integer('price')->default(0)->comment('价格(分)');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
