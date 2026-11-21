<?php

namespace App\Livewire\MasterData;

use App\Livewire\Concerns\ResetsPageOnSearch;
use App\Livewire\Concerns\WithRoleLayout;
use App\Livewire\Concerns\WithSwal;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithPagination;

abstract class BaseCrud extends Component
{
    use WithPagination, WithRoleLayout, WithSwal, ResetsPageOnSearch;

    public string $search = '';
    public bool $isModalOpen = false;
    public ?int $modelId = null;

    /** Field yang di-reset saat openModal/closeModal. Diisi otomatis dari fields(). */
    public array $form = [];

    // =========================================================
    // CONTRACT — Child class WAJIB override
    // =========================================================

    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** @return array<string> Nama field form (urut sesuai form) */
    abstract protected function fields(): array;

    /** @return array<string, mixed> Aturan validasi */
    abstract protected function rules(): array;

    // =========================================================
    // HOOKS — Child class BOLEH override
    // =========================================================

    /** Field yang bisa dicari (default: fields()). */
    protected function searchable(): array
    {
        return $this->fields();
    }

    /** Nilai default form saat tambah baru. */
    protected function defaultValues(): array
    {
        return [];
    }

    /** Data tambahan untuk view (select options, dll). */
    protected function extraViewData(): array
    {
        return [];
    }

    /** Urutan query default. */
    protected function orderBy(): string
    {
        return 'id';
    }

    /** Jumlah per halaman. */
    protected function perPage(): int
    {
        return 10;
    }

    /**
     * Hook sebelum save. Return [] untuk abort (misal: duplikat).
     * Bisa juga panggil $this->swalError() di sini.
     */
    protected function beforeSave(array $data): array
    {
        return $data;
    }

    /**
     * Hook sebelum delete. Return string error untuk abort.
     * Return null untuk lanjut hapus.
     */
    protected function beforeDelete(Model $model): ?string
    {
        return null;
    }

    /**
     * Label entitas untuk pesan sukses/gagal (misal: "Divisi", "Akun").
     * Default: nama class model.
     */
    protected function entityLabel(): string
    {
        return class_basename($this->model());
    }

    // =========================================================
    // PUBLIC ACTIONS — Tidak perlu override
    // =========================================================

    public function openModal(): void
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function edit(int $id): void
    {
        $model = $this->model()::findOrFail($id);
        $this->modelId = $model->id;

        foreach ($this->fields() as $field) {
            $this->form[$field] = $model->{$field};
        }

        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = $this->beforeSave($this->form);

        if (empty($data)) {
            return; // abort dari hook
        }

        $this->model()::updateOrCreate(['id' => $this->modelId], $data);

        $label = $this->entityLabel();
        $this->swalSuccess(
            $this->modelId
                ? "Data {$label} berhasil diperbarui."
                : "Data {$label} berhasil ditambahkan."
        );

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        $model = $this->model()::findOrFail($id);

        if ($error = $this->beforeDelete($model)) {
            $this->swalError($error);
            return;
        }

        $model->delete();
        $this->swalSuccess("Data {$this->entityLabel()} berhasil dihapus.");
    }

    // =========================================================
    // RENDER
    // =========================================================

    public function render()
    {
        $query = $this->model()::query();

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                foreach ($this->searchable() as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        $models = $query->orderBy($this->orderBy())->paginate($this->perPage());

        return view($this->viewName(), array_merge(
            [$this->viewVariableName() => $models],
            $this->extraViewData()
        ))->layout($this->resolveLayout());
    }

    /** @return string Nama view blade (auto-resolve dari nama class). */
    protected function viewName(): string
    {
        // GolonganIndex -> livewire.master-data.golongan-index
        $class = class_basename(static::class);
        $kebab = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $class));

        return "livewire.master-data.{$kebab}";
    }

    /** @return string Nama variabel yang dikirim ke view (golongan, divisi, dst). */
    protected function viewVariableName(): string
    {
        // GolonganIndex -> golongans
        $class = str_replace('Index', '', class_basename(static::class));

        return strtolower($class) . 's';
    }

    protected function resetFields(): void
    {
        $this->modelId = null;

        $defaults = $this->defaultValues();
        $this->form = [];

        foreach ($this->fields() as $field) {
            $this->form[$field] = $defaults[$field] ?? null;
        }

        $this->resetValidation();
    }
}