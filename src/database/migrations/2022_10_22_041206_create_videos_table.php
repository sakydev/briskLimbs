<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('vkey');
            $table->string('filename');
            $table->string('title');
            $table->text('description');
            $table->string('state'); // active, inactive
            $table->string('status'); // pending, processing, success, failed
            $table->string('scope'); // public, private, unlisted
            $table->integer('duration');
            $table->string('directory');
            $table->integer('default_thumbnail');
            $table->string('qualities');
            $table->string('tags');
            $table->integer('total_views');
            $table->integer('total_comments');
            $table->integer('allow_comments'); // 1: yes, 0:no
            $table->integer('allow_embed'); // 1: yes, 0:no
            $table->integer('allow_download'); // 1: yes, 0:no
            $table->string('server_url');
            $table->timestamp('converted_at')->useCurrent();

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
        Schema::dropIfExists('videos');
    }
};
