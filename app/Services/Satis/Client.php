<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/2/23
 * Time: 11:20
 */

namespace App\Services\Satis;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Client
{
    protected array $config;

    /**
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * satis执行文件路径
     *
     * @return string
     */
    public function getBin(): string
    {
        $bin = Arr::get($this->config, 'bin');
        return $bin ?: base_path('vendor/bin/satis');
    }

    /**
     * satis运行目录
     *
     * @return string
     */
    public function getRuntimeDir(): string
    {
        $dir = Arr::get($this->config, 'runtime_dir');
        return $dir ?: storage_path('satis');
    }

    /**
     * composer home目录
     *
     * @return string
     */
    public function getComposerDir(): string
    {
        $dir = Arr::get($this->config, 'composer_dir');
        return $dir ?: sprintf("%s/composer", $this->getRuntimeDir());
    }

    /**
     * 构建物目录
     *
     * @return string
     */
    public function getBuildDir(): string
    {
        $dir = Arr::get($this->config, 'build_dir');
        return $dir ?: sprintf("%s/build", $this->getRuntimeDir());
    }

    /**
     * satis配置文件
     *
     * @return string
     */
    public function getSatisConfigFile(): string
    {
        return sprintf("%s/config.json", $this->getRuntimeDir());
    }

    /**
     * 构建物的索引目录
     *
     * @return string
     */
    public function getBuiltIndexFile(): string
    {
        return sprintf('%s/packages.json', $this->getBuildDir());
    }

    /**
     * @return array
     */
    public function getSatisConfig(): array
    {
        return Arr::get($this->config, 'satis', []);
    }

    /**
     * 合并已经构建的包
     *
     * @return $this
     */
    public function mergeBuiltPackages(): static
    {
        $file = $this->getBuiltIndexFile();
        if (!is_file($file)) {
            return $this;
        }

        $packages = json_decode(file_get_contents($file), true);
        $includes = empty($packages["includes"]) ? [] : $packages["includes"];

        foreach ($includes as $subFile => $include) {
            $subFile = sprintf("%s/%s", $this->getBuildDir(), $subFile);
            $subPackages = json_decode(file_get_contents($subFile), true);
            $subPackages = empty($subPackages["packages"]) ? [] : $subPackages["packages"];

            foreach ($subPackages as $package) {
                foreach ($package as $version) {
                    $this->addPackage($version["source"]["url"], $version["source"]["type"]);
                }
            }
        }

        return $this;
    }

    /**
     * 添加一个包
     *
     * @param  string  $url
     * @param  string  $type
     * @return $this
     */
    public function addPackage(string $url, string $type = 'git'): static
    {
        $this->config['satis']['repositories'] = Collection::make($this->getPackages())
            ->add(["type" => $type, "url" => $url])
            ->keyBy(function ($item) {
                return sprintf("%s-%s", $item["type"], $item["url"]);
            })
            ->values()
            ->toArray();

        return $this;
    }

    protected function deletePackage(string $url, string $type = 'git'): static
    {
        $packages = $this->getPackages();
        foreach ($packages as $index => $package) {
            if ($package['url'] === $url && $package['url'] === $type) {
                unset($packages[$index]);
            }
        }
        $this->config['satis']['repositories'] = array_values($packages);

        return $this;
    }

    public function getPackages()
    {
        return Arr::get($this->getSatisConfig(), 'repositories', []);
    }

    public function dumpConfig(): static
    {
        $data = json_encode($this->getSatisConfig(), 448);
        file_put_contents($this->getSatisConfigFile(), $data, LOCK_EX);

        return $this;
    }

    public function reset(): static
    {
        File::cleanDirectory($this->getComposerDir());
        File::cleanDirectory($this->getRuntimeDir());

        is_dir($this->getComposerDir()) or File::makeDirectory($this->getComposerDir(), 0755, true);
        is_dir($this->getRuntimeDir()) or File::makeDirectory($this->getRuntimeDir(), 0755, true);

        return $this;
    }
}
