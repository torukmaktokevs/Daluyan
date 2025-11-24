<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_document_types_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTypesTable extends Migration
{
    public function up()
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Lease Agreement, ID Proof, Payment Receipt, etc.
            $table->string('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->json('allowed_extensions')->nullable(); // ['pdf', 'jpg', 'png']
            $table->integer('max_size')->default(2048); // in KB
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_types');
    }
}